<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingUnit;
use App\Models\BookingUnitNight;
use App\Models\BookingStatusHistory;
use App\Models\VillaType;
use App\Models\VillaUnit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class BookingCreationService
{
    public function __construct(
        private PricingService $pricingService
    ) {
    }

    public function create(array $data): Booking
    {
        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();
        $guestCount = (int) $data['guest_count'];

        if ($checkIn->greaterThanOrEqualTo($checkOut)) {
            throw new InvalidArgumentException(
                'Check-out harus setelah check-in.'
            );
        }

        if ($checkIn->lt(today())) {
            throw new InvalidArgumentException(
                'Tanggal check-in tidak boleh di masa lalu.'
            );
        }

        return DB::transaction(function () use (
            $data, $checkIn, $checkOut, $guestCount
        ) {
            // Kunci tipe villa agar proses booking pada tipe yang sama
            // berjalan bergantian, bukan memilih unit bersamaan.
            $villaType = VillaType::query()
                ->whereKey($data['villa_type_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$villaType) {
                throw new RuntimeException(
                    'Tipe villa tidak ditemukan atau tidak aktif.'
                );
            }

            if (
                $guestCount < 1
                || $guestCount > $villaType->max_capacity
            ) {
                throw new InvalidArgumentException(
                    'Jumlah tamu melebihi kapasitas villa.'
                );
            }

            // Kunci unit dengan urutan konsisten.
            $units = VillaUnit::query()
                ->where('villa_type_id', $villaType->id)
                ->where('condition_status', 'good')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $selectedUnit = null;

            foreach ($units as $unit) {
                $isBlocked = $unit->unitBlocks()
                    ->where('start_date', '<', $checkOut->toDateString())
                    ->where('end_date', '>', $checkIn->toDateString())
                    ->exists();

                if ($isBlocked) {
                    continue;
                }

                $hasBooking = $unit->bookingUnits()
                    ->whereHas('booking', function ($query) use (
                        $checkIn, $checkOut
                    ) {
                        $query
                            ->where('check_in', '<', $checkOut->toDateString())
                            ->where('check_out', '>', $checkIn->toDateString())
                            ->where(function ($status) {
                                $status
                                    ->whereIn('status', [
                                        'confirmed',
                                        'checked_in',
                                    ])
                                    ->orWhere(function ($pending) {
                                        $pending
                                            ->where('status', 'pending_payment')
                                            ->where('hold_expires_at', '>', now());
                                    });
                            });
                    })
                    ->exists();

                if (!$hasBooking) {
                    $selectedUnit = $unit;
                    break;
                }
            }

            if (!$selectedUnit) {
                throw new RuntimeException(
                    'Maaf, unit tidak tersedia pada tanggal tersebut.'
                );
            }

            $pricing = $this->pricingService->calculate(
                $villaType->id,
                $checkIn->toDateString(),
                $checkOut->toDateString()
            );

            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode(),
                'public_token' => Str::random(64),
                'branch_id' => $villaType->branch_id,
                'user_id' => $data['user_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'guest_count' => $guestCount,
                'source' => $data['source'] ?? 'website',
                'status' => 'pending_payment',
                'payment_status' => 'unpaid',
                'subtotal' => $pricing['subtotal'],
                'discount_amount' => 0,
                'tax_amount' => 0,
                'service_charge' => 0,
                'total_amount' => $pricing['subtotal'],
                'hold_expires_at' => now()->addMinutes(20),
                'special_requests' => $data['special_requests'] ?? null,
            ]);

            $bookingUnit = BookingUnit::create([
                'booking_id' => $booking->id,
                'villa_unit_id' => $selectedUnit->id,
                'guest_count' => $guestCount,
                'subtotal' => $pricing['subtotal'],
            ]);

            foreach ($pricing['nights'] as $night) {
                BookingUnitNight::create([
                    'booking_unit_id' => $bookingUnit->id,
                    'stay_date' => $night['stay_date'],
                    'nightly_price' => $night['nightly_price'],
                    'price_type' => $night['price_type'],
                ]);
            }

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => null,
                'to_status' => 'pending_payment',
                'changed_by' => null,
                'notes' => 'Booking dibuat; menunggu pembayaran.',
            ]);

            return $booking->load([
                'bookingUnits.villaUnit',
                'bookingUnits.nights',
            ]);
        }, 3);
    }

    private function generateBookingCode(): string
    {
        do {
            $code = 'VILLA-' . now()->format('Ymd')
                . '-' . Str::upper(Str::random(6));
        } while (
            Booking::where('booking_code', $code)->exists()
        );

        return $code;
    }
}
