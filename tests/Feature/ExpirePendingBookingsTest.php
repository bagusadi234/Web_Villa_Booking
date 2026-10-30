<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\BookingStatusHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExpirePendingBookingsTest extends TestCase
{
    use RefreshDatabase;

    protected function createBooking(
        string $status = 'pending_payment',
        string $paymentStatus = 'unpaid',
        ?Carbon $holdExpiresAt = null
    ): Booking {
        $branch = Branch::create([
            'name' => 'Cabang Expire Test',
            'slug' => 'cabang-expire-test',
            'is_active' => true,
        ]);

        return Booking::create([
            'booking_code' => 'TEST-' . uniqid(),
            'public_token' => bin2hex(random_bytes(32)),
            'branch_id' => $branch->id,
            'customer_name' => 'Pelanggan Test',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guest_count' => 2,
            'source' => 'website',
            'status' => $status,
            'payment_status' => $paymentStatus,
            'subtotal' => 1000000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'service_charge' => 0,
            'total_amount' => 1000000,
            'hold_expires_at' => $holdExpiresAt,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(
            Carbon::parse('2026-10-30 10:00:00')
        );
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_expired_unpaid_booking_is_updated(): void
    {
        $booking = $this->createBooking(
            'pending_payment',
            'unpaid',
            now()->subMinute()
        );

        Artisan::call('bookings:expire');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'expired',
        ]);
    }

    public function test_paid_booking_is_not_expired(): void
    {
        $booking = $this->createBooking(
            'pending_payment',
            'paid',
            now()->subMinute()
        );

        Artisan::call('bookings:expire');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'pending_payment',
            'payment_status' => 'paid',
        ]);
    }

    public function test_booking_with_active_hold_is_not_expired(): void
    {
        $booking = $this->createBooking(
            'pending_payment',
            'unpaid',
            now()->addMinutes(10)
        );

        Artisan::call('bookings:expire');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'pending_payment',
        ]);
    }

    public function test_expiring_booking_creates_status_history(): void
    {
        $booking = $this->createBooking(
            'pending_payment',
            'unpaid',
            now()->subMinute()
        );

        Artisan::call('bookings:expire');

        $this->assertDatabaseHas('booking_status_history', [
            'booking_id' => $booking->id,
            'from_status' => 'pending_payment',
            'to_status' => 'expired',
        ]);

        $this->assertSame(1, $booking->statusHistory()->count());
    }

    public function test_running_expire_command_twice_does_not_duplicate_history(): void
    {
        $booking = $this->createBooking(
            'pending_payment',
            'unpaid',
            now()->subMinute()
        );

        Artisan::call('bookings:expire');
        Artisan::call('bookings:expire');

        $this->assertSame(1, $booking->statusHistory()->count());

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'expired',
        ]);
    }
}
