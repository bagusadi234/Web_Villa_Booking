<?php

namespace App\Services;

use App\Models\VillaUnit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class AvailabilityService
{
    /**
     * Mendapatkan unit yang tersedia untuk periode menginap.
     *
     * Tanggal check-out tidak dihitung sebagai malam menginap.
     */
    public function availableUnits(
        int $villaTypeId,
        string $checkIn,
        string $checkOut
    ): Collection {
        $startDate = Carbon::parse($checkIn)->startOfDay();
        $endDate = Carbon::parse($checkOut)->startOfDay();

        if ($startDate->greaterThanOrEqualTo($endDate)) {
            throw new InvalidArgumentException(
                'Tanggal check-out harus setelah tanggal check-in.'
            );
        }

        return VillaUnit::query()
            ->where('villa_type_id', $villaTypeId)
            ->where('condition_status', 'good')

            // Unit tidak boleh sedang diblokir.
            ->whereDoesntHave('unitBlocks', function ($query) use (
                $startDate,
                $endDate
            ) {
                $query
                    ->whereDate('start_date', '<', $endDate)
                    ->whereDate('end_date', '>', $startDate);
            })

            // Unit tidak boleh memiliki booking yang bertabrakan.
            ->whereDoesntHave('bookingUnits', function ($query) use (
                $startDate,
                $endDate
            ) {
                $query->whereHas('booking', function ($booking) use (
                    $startDate,
                    $endDate
                ) {
                    $booking
                        ->whereDate('check_in', '<', $endDate)
                        ->whereDate('check_out', '>', $startDate)
                        ->where(function ($status) {
                            $status
                                ->whereIn('status', [
                                    'confirmed',
                                    'checked_in',
                                ])
                                ->orWhere(function ($pending) {
                                    $pending
                                        ->where(
                                            'status',
                                            'pending_payment'
                                        )
                                        ->where(
                                            'hold_expires_at',
                                            '>',
                                            now()
                                        );
                                });
                        });
                });
            })
            ->orderBy('unit_code')
            ->get();
    }
}
