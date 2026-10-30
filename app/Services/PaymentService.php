<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\BookingStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PaymentService
{
    /**
     * Mencatat permintaan pembayaran DP atau penuh.
     *
     * Pembayaran tetap pending sampai diverifikasi
     * oleh proses tepercaya.
     */
    public function createPayment(
        int $bookingId,
        string $type,
        int $amount,
        string $method = 'other'
    ): Payment {
        if (!in_array($type, ['dp', 'full_payment'], true)) {
            throw new InvalidArgumentException(
                'Jenis pembayaran harus DP atau pembayaran penuh.'
            );
        }

        if ($amount < 1) {
            throw new InvalidArgumentException(
                'Nominal pembayaran harus lebih dari nol.'
            );
        }

        if (!in_array($method, [
            'midtrans',
            'xendit',
            'bank_transfer',
            'cash',
            'other',
        ], true)) {
            throw new InvalidArgumentException(
                'Metode pembayaran tidak valid.'
            );
        }

        return DB::transaction(function () use (
            $bookingId,
            $type,
            $amount,
            $method
        ) {
            $booking = Booking::query()
                ->whereKey($bookingId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($booking->status !== 'pending_payment') {
                throw ValidationException::withMessages([
                    'payment' => 'Booking ini tidak sedang menunggu pembayaran.',
                ]);
            }

            if (
                $booking->hold_expires_at === null
                || $booking->hold_expires_at->isPast()
            ) {
                throw ValidationException::withMessages([
                    'payment' => 'Waktu pembayaran telah berakhir.',
                ]);
            }

            if ($booking->payment_status !== 'unpaid') {
                throw ValidationException::withMessages([
                    'payment' => 'Booking ini sudah memiliki pembayaran.',
                ]);
            }

            $total = (int) $booking->total_amount;

            if ($total < 1 || $amount > $total) {
                throw new InvalidArgumentException(
                    'Nominal pembayaran melebihi total tagihan atau total tidak valid.'
                );
            }

            if ($type === 'full_payment' && $amount !== $total) {
                throw new InvalidArgumentException(
                    'Pembayaran penuh harus sama dengan total tagihan.'
                );
            }

            if ($type === 'dp' && $amount >= $total) {
                throw new InvalidArgumentException(
                    'Nominal DP harus lebih kecil dari total tagihan.'
                );
            }

            $hasPendingPayment = $booking->payments()
                ->where('status', 'pending')
                ->exists();

            if ($hasPendingPayment) {
                throw ValidationException::withMessages([
                    'payment' => 'Masih ada transaksi pembayaran yang menunggu proses.',
                ]);
            }

            return $booking->payments()->create([
                'transaction_type' => $type,
                'payment_method' => $method,
                'amount' => $amount,
                'status' => 'pending',
            ]);
        }, 3);
    }

    /**
     * Konfirmasi pembayaran yang SUDAH diverifikasi
     * oleh gateway atau proses admin tepercaya.
     *
     * Jangan panggil metode ini langsung dari endpoint publik
     * tanpa verifikasi autentikasi/signature dan sumber transaksi.
     */
    public function confirmVerifiedPayment(
        int $paymentId,
        int $verifiedAmount,
        ?string $externalTransactionId = null,
        array $gatewayResponse = []
    ): Payment {
        if ($verifiedAmount < 1) {
            throw new InvalidArgumentException(
                'Nominal pembayaran terverifikasi tidak valid.'
            );
        }

        return DB::transaction(function () use (
            $paymentId,
            $verifiedAmount,
            $externalTransactionId,
            $gatewayResponse
        ) {
            $payment = Payment::query()
                ->whereKey($paymentId)
                ->lockForUpdate()
                ->firstOrFail();

            $booking = Booking::query()
                ->whereKey($payment->booking_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Nominal harus cocok dengan transaksi yang dibuat.
            if ((int) $payment->amount !== $verifiedAmount) {
                throw ValidationException::withMessages([
                    'payment' => 'Nominal terverifikasi tidak cocok dengan transaksi.',
                ]);
            }

            // Jika callback dikirim ulang, jangan memproses pembayaran lagi.
            if ($payment->status === 'success') {
                if (
                    $externalTransactionId !== null
                    && $payment->external_transaction_id !== null
                    && $payment->external_transaction_id !== $externalTransactionId
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'ID transaksi eksternal tidak cocok.',
                    ]);
                }

                return $payment;
            }

            if ($payment->status !== 'pending') {
                throw ValidationException::withMessages([
                    'payment' => 'Transaksi ini tidak lagi menunggu pembayaran.',
                ]);
            }

            // Cegah dua pembayaran sukses untuk booking yang sama.
            $anotherSuccessfulPayment = $booking->payments()
                ->where('id', '!=', $payment->id)
                ->where('status', 'success')
                ->exists();

            if ($anotherSuccessfulPayment) {
                throw ValidationException::withMessages([
                    'payment' => 'Booking ini sudah memiliki pembayaran berhasil. Perlu pemeriksaan manual.',
                ]);
            }

            $now = now();

            $payment->status = 'success';
            $payment->paid_at = $now;

            if ($externalTransactionId !== null) {
                $payment->external_transaction_id = $externalTransactionId;
            }

            $payment->gateway_response = $gatewayResponse;
            $payment->save();

            if ($payment->transaction_type === 'dp') {
                $booking->payment_status = 'partially_paid';
            } elseif ($payment->transaction_type === 'full_payment') {
                $booking->payment_status = 'paid';
            }

            // Booking hanya otomatis dikonfirmasi jika pembayaran penuh
            // terverifikasi dan masa hold masih berlaku.
            // Pembayaran setelah hold berakhir perlu penanganan manual.
            $holdIsActive = $booking->hold_expires_at !== null
                && $booking->hold_expires_at->isFuture();

            if (
                $payment->transaction_type === 'full_payment'
                && $booking->status === 'pending_payment'
                && $holdIsActive
            ) {
                $previousStatus = $booking->status;
                $booking->status = 'confirmed';
                $booking->save();

                BookingStatusHistory::query()->create([
                    'booking_id' => $booking->id,
                    'from_status' => $previousStatus,
                    'to_status' => 'confirmed',
                    'changed_by' => null,
                    'notes' => 'Booking dikonfirmasi setelah pembayaran penuh terverifikasi.',
                ]);
            } else {
                $booking->save();
            }

            return $payment->fresh();
        }, 3);
    }
}
