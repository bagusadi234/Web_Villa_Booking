<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingBookings extends Command
{
    protected $signature = 'bookings:expire';

    protected $description =
        'Mengubah booking yang melewati batas waktu hold menjadi expired';

    public function handle(): int
    {
        $expiredCount = DB::transaction(function () {
            $bookings = Booking::query()
                ->where('status', 'pending_payment')
                ->where('payment_status', 'unpaid')
                ->whereNotNull('hold_expires_at')
                ->where('hold_expires_at', '<=', now())
                ->lockForUpdate()
                ->get();

            foreach ($bookings as $booking) {
                $booking->update([
                    'status' => 'expired',
                ]);

                BookingStatusHistory::create([
                    'booking_id' => $booking->id,
                    'from_status' => 'pending_payment',
                    'to_status' => 'expired',
                    'changed_by' => null,
                    'notes' => 'Booking kedaluwarsa karena batas waktu pembayaran habis.',
                ]);
            }

            return $bookings->count();
        }, 3);

        $this->info(
            "Jumlah booking yang kedaluwarsa: {$expiredCount}"
        );

        return self::SUCCESS;
    }
}
