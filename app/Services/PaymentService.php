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
     * Membuat transaksi DP, pembayaran penuh, atau pelunasan.
     *
     * DP wajib 30% dari total booking.
     * Pelunasan wajib sama dengan sisa tagihan.
     */
    public function createPayment(
        int $bookingId,
        string $type,
        int $amount,
        string $method = 'other'
    ): Payment {
        if (!in_array($type, [
            'dp',
            'full_payment',
            'remaining_payment',
        ], true)) {
            throw new InvalidArgumentException(
                'Jenis pembayaran tidak valid.'
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

            $total = (int) $booking->total_amount;

            if ($total < 1) {
                throw new InvalidArgumentException(
                    'Total booking tidak valid.'
                );
            }

            // Hold hanya berlaku untuk pembayaran awal.
            // Pelunasan dilakukan setelah DP, termasuk saat check-in.
            if (
                in_array($type, ['dp', 'full_payment'], true)
                && (
                    $booking->hold_expires_at === null
                    || !$booking->hold_expires_at->isFuture()
                )
            ) {
                throw ValidationException::withMessages([
                    'payment' => 'Waktu pembayaran telah berakhir.',
                ]);
            }

            $hasPendingPayment = $booking->payments()
                ->where('status', 'pending')
                ->exists();

            if ($hasPendingPayment) {
                throw ValidationException::withMessages([
                    'payment' => 'Masih ada transaksi pembayaran yang menunggu proses.',
                ]);
            }

            $successfulPayments = $booking->payments()
                ->where('status', 'success')
                ->get();

            $totalPaid = (int) $successfulPayments->sum('amount');
            $remainingAmount = $total - $totalPaid;

            if ($remainingAmount <= 0) {
                throw ValidationException::withMessages([
                    'payment' => 'Booking ini sudah lunas.',
                ]);
            }

            if ($type === 'dp') {
                $expectedDp = intdiv(($total * 30) + 99, 100);

                if (
                    $booking->status !== 'pending_payment'
                    || $booking->payment_status !== 'unpaid'
                    || $totalPaid !== 0
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'DP hanya dapat dibuat untuk booking yang belum dibayar.',
                    ]);
                }

                if ($amount !== $expectedDp) {
                    throw new InvalidArgumentException(
                        'Nominal DP harus tepat 30% dari total booking.'
                    );
                }
            }

            if ($type === 'full_payment') {
                if (
                    $booking->status !== 'pending_payment'
                    || $booking->payment_status !== 'unpaid'
                    || $totalPaid !== 0
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'Pembayaran penuh hanya dapat dibuat untuk booking yang belum dibayar.',
                    ]);
                }

                if ($amount !== $total) {
                    throw new InvalidArgumentException(
                        'Pembayaran penuh harus sama dengan total tagihan.'
                    );
                }
            }

            if ($type === 'remaining_payment') {
                $hasSuccessfulDp = $successfulPayments->contains(
                    fn (Payment $payment) =>
                        $payment->transaction_type === 'dp'
                );

                if (
                    $booking->status !== 'confirmed'
                    || $booking->payment_status !== 'partially_paid'
                    || !$hasSuccessfulDp
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'Pelunasan hanya dapat dilakukan setelah DP berhasil dan booking dikonfirmasi.',
                    ]);
                }

                if ($amount !== $remainingAmount) {
                    throw new InvalidArgumentException(
                        'Nominal pelunasan harus sama dengan sisa tagihan.'
                    );
                }
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
     * Mengonfirmasi pembayaran yang sudah diverifikasi
     * oleh gateway atau proses admin tepercaya.
     *
     * Endpoint publik wajib memverifikasi signature webhook
     * dan identitas transaksi sebelum memanggil metode ini.
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
            // Ambil booking_id terlebih dahulu tanpa mengunci payment.
            $paymentReference = Payment::query()
                ->select(['id', 'booking_id'])
                ->whereKey($paymentId)
                ->firstOrFail();

            // Urutan lock konsisten: Booking dahulu, lalu Payment.
            $booking = Booking::query()
                ->whereKey($paymentReference->booking_id)
                ->lockForUpdate()
                ->firstOrFail();

            $payment = Payment::query()
                ->whereKey($paymentId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $payment->booking_id !== (int) $booking->id) {
                throw ValidationException::withMessages([
                    'payment' => 'Referensi booking transaksi tidak valid.',
                ]);
            }

            if ((int) $payment->amount !== $verifiedAmount) {
                throw ValidationException::withMessages([
                    'payment' => 'Nominal terverifikasi tidak cocok dengan transaksi.',
                ]);
            }

            // Callback berulang tidak boleh memproses transaksi lagi.
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

            // Cegah penggunaan ID transaksi eksternal yang sama.
            if ($externalTransactionId !== null) {
                $duplicateExternalId = Payment::query()
                    ->where('external_transaction_id', $externalTransactionId)
                    ->where('id', '!=', $payment->id)
                    ->exists();

                if ($duplicateExternalId) {
                    throw ValidationException::withMessages([
                        'payment' => 'ID transaksi eksternal sudah digunakan.',
                    ]);
                }
            }

            $total = (int) $booking->total_amount;

            $successfulPayments = $booking->payments()
                ->where('status', 'success')
                ->get();

            $totalPaid = (int) $successfulPayments->sum('amount');
            $remainingAmount = $total - $totalPaid;

            if ($payment->transaction_type === 'dp') {
                $expectedDp = intdiv(($total * 30) + 99, 100);

                if (
                    $booking->payment_status !== 'unpaid'
                    || $booking->status !== 'pending_payment'
                    || $totalPaid !== 0
                    || (int) $payment->amount !== $expectedDp
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'Transaksi DP tidak sesuai dengan kondisi booking.',
                    ]);
                }
            } elseif ($payment->transaction_type === 'full_payment') {
                if (
                    $booking->payment_status !== 'unpaid'
                    || $booking->status !== 'pending_payment'
                    || $totalPaid !== 0
                    || (int) $payment->amount !== $total
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'Transaksi pembayaran penuh tidak sesuai dengan kondisi booking.',
                    ]);
                }
            } elseif ($payment->transaction_type === 'remaining_payment') {
                $hasSuccessfulDp = $successfulPayments->contains(
                    fn (Payment $item) =>
                        $item->transaction_type === 'dp'
                );

                if (
                    $booking->status !== 'confirmed'
                    || $booking->payment_status !== 'partially_paid'
                    || !$hasSuccessfulDp
                    || (int) $payment->amount !== $remainingAmount
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'Transaksi pelunasan tidak sesuai dengan sisa tagihan.',
                    ]);
                }
            } else {
                throw ValidationException::withMessages([
                    'payment' => 'Jenis transaksi ini belum didukung oleh proses konfirmasi.',
                ]);
            }

            $payment->status = 'success';
            $payment->paid_at = now();

            if ($externalTransactionId !== null) {
                $payment->external_transaction_id = $externalTransactionId;
            }

            $payment->gateway_response = $gatewayResponse;
            $payment->save();

            if ($payment->transaction_type === 'dp') {
                $booking->payment_status = 'partially_paid';
            } else {
                $booking->payment_status = 'paid';
            }

            // DP maupun pembayaran penuh mengonfirmasi booking
            // hanya selama masa hold masih aktif.
            if (
                in_array($payment->transaction_type, [
                    'dp',
                    'full_payment',
                ], true)
                && $booking->status === 'pending_payment'
                && $booking->hold_expires_at !== null
                && $booking->hold_expires_at->isFuture()
            ) {
                $previousStatus = $booking->status;
                $booking->status = 'confirmed';
                $booking->save();

                BookingStatusHistory::query()->create([
                    'booking_id' => $booking->id,
                    'from_status' => $previousStatus,
                    'to_status' => 'confirmed',
                    'changed_by' => null,
                    'notes' => $payment->transaction_type === 'dp'
                        ? 'Booking dikonfirmasi setelah pembayaran DP 30% terverifikasi.'
                        : 'Booking dikonfirmasi setelah pembayaran penuh terverifikasi.',
                ]);
            } else {
                // Pembayaran yang masuk setelah hold berakhir
                // dicatat, tetapi booking tidak diaktifkan kembali.
                $booking->save();
            }

            return $payment->fresh();
        }, 3);
    }
}
