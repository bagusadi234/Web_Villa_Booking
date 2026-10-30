<?php

namespace Tests\Feature;


use App\Models\Booking;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;
use App\Models\Branch;
use App\Models\VillaType;
use App\Models\VillaUnit;
use App\Models\PricingRule;
use App\Services\BookingCreationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class BookingConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_second_booking_is_rejected_when_unit_is_on_active_hold(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Test',
            'slug' => 'cabang-test',
            'is_active' => true,
        ]);

        $villaType = VillaType::create([
            'branch_id' => $branch->id,
            'name' => 'Villa Test',
            'slug' => 'villa-test',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'standard_capacity' => 2,
            'max_capacity' => 4,
            'is_active' => true,
        ]);

        VillaUnit::create([
            'villa_type_id' => $villaType->id,
            'unit_code' => 'TEST-01',
            'unit_name' => 'Unit Test',
            'condition_status' => 'good',
        ]);

        PricingRule::create([
            'villa_type_id' => $villaType->id,
            'rule_type' => 'weekday',
            'price' => 500000,
            'start_date' => null,
            'end_date' => null,
            'is_active' => true,
        ]);

        PricingRule::create([
            'villa_type_id' => $villaType->id,
            'rule_type' => 'weekend',
            'price' => 600000,
            'start_date' => null,
            'end_date' => null,
            'is_active' => true,
        ]);

        $service = app(BookingCreationService::class);

        $bookingData = [
            'villa_type_id' => $villaType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guest_count' => 2,
            'customer_name' => 'Pelanggan Test',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
        ];

        $firstBooking = $service->create($bookingData);

        $this->assertSame('pending_payment', $firstBooking->status);
        $this->assertSame(1, $firstBooking->bookingUnits()->count());

        try {
            $service->create($bookingData);
            $this->fail('Booking kedua seharusnya ditolak.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Maaf, unit tidak tersedia pada tanggal tersebut.',
                $exception->getMessage()
            );
        }

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('booking_units', 1);
    }

    public function test_unit_can_be_booked_again_after_previous_hold_expires(): void
    {
        Carbon::setTestNow(now()->startOfDay()->addHours(10));

        try {
            $branch = Branch::create([
                'name' => 'Cabang Expired Test',
                'slug' => 'cabang-expired-test',
                'is_active' => true,
            ]);

            $villaType = VillaType::create([
                'branch_id' => $branch->id,
                'name' => 'Villa Expired Test',
                'slug' => 'villa-expired-test',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'standard_capacity' => 2,
                'max_capacity' => 3,
                'is_active' => true,
            ]);

            VillaUnit::create([
                'villa_type_id' => $villaType->id,
                'unit_code' => 'EXP-01',
                'unit_name' => 'Unit Expired Test',
                'condition_status' => 'good',
            ]);

            PricingRule::create([
                'villa_type_id' => $villaType->id,
                'rule_type' => 'weekday',
                'price' => 500000,
                'start_date' => null,
                'end_date' => null,
                'is_active' => true,
            ]);

            PricingRule::create([
                'villa_type_id' => $villaType->id,
                'rule_type' => 'weekend',
                'price' => 600000,
                'start_date' => null,
                'end_date' => null,
                'is_active' => true,
            ]);

            $service = app(BookingCreationService::class);

            $bookingData = [
                'villa_type_id' => $villaType->id,
                'check_in' => now()->addDays(10)->toDateString(),
                'check_out' => now()->addDays(12)->toDateString(),
                'guest_count' => 2,
                'customer_name' => 'Pelanggan Expired Test',
                'customer_email' => 'expired@example.com',
                'customer_phone' => '081234567890',
            ];

            $firstBooking = $service->create($bookingData);

            $this->assertSame('pending_payment', $firstBooking->status);

            // Simulasikan waktu setelah masa hold 20 menit berakhir.
            Carbon::setTestNow(now()->addMinutes(21));

            Artisan::call('bookings:expire');

            $this->assertDatabaseHas('bookings', [
                'id' => $firstBooking->id,
                'status' => 'expired',
            ]);

            // Unit yang sama seharusnya bisa dipesan kembali.
            $secondBooking = $service->create($bookingData);

            $this->assertSame('pending_payment', $secondBooking->status);
            $this->assertDatabaseCount('bookings', 2);
            $this->assertDatabaseCount('booking_units', 2);
        } finally {
            Carbon::setTestNow();
        }
    }
}
