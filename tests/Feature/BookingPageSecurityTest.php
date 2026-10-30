<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\VillaType;
use App\Models\VillaUnit;
use App\Models\PricingRule;
use App\Services\BookingCreationService;
use App\Services\PricingService;
use InvalidArgumentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPageSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function createTestBooking()
    {
        $branch = Branch::create([
            'name' => 'Cabang Security Test',
            'slug' => 'cabang-security-test',
            'is_active' => true,
        ]);

        $villaType = VillaType::create([
            'branch_id' => $branch->id,
            'name' => 'Villa Security Test',
            'slug' => 'villa-security-test',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'standard_capacity' => 2,
            'max_capacity' => 4,
            'is_active' => true,
        ]);

        VillaUnit::create([
            'villa_type_id' => $villaType->id,
            'unit_code' => 'SEC-01',
            'unit_name' => 'Unit Security Test',
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
            'price' => 500000,
            'start_date' => null,
            'end_date' => null,
            'is_active' => true,
        ]);

        return app(BookingCreationService::class)->create([
            'villa_type_id' => $villaType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guest_count' => 2,
            'customer_name' => 'Pelanggan Security Test',
            'customer_email' => 'security@example.com',
            'customer_phone' => '081234567890',
        ]);
    }

    public function test_booking_page_returns_404_for_unknown_token(): void
    {
        $token = str_repeat('a', 64);

        $response = $this->get('/booking/' . $token);

        $response->assertNotFound();
    }

    public function test_booking_page_rejects_token_with_invalid_format(): void
    {
        $response = $this->get('/booking/token-tidak-valid');

        $response->assertNotFound();
    }

    public function test_valid_token_opens_the_correct_booking_page(): void
    {
        $booking = $this->createTestBooking();

        $response = $this->get('/booking/' . $booking->public_token);

        $response->assertOk();
        $response->assertViewIs('bookings.show');
        $response->assertViewHas('booking', function ($viewBooking) use ($booking) {
            return $viewBooking->id === $booking->id
                && $viewBooking->public_token === $booking->public_token;
        });
    }

    public function test_booking_page_rejects_numeric_booking_id_instead_of_token(): void
    {
        $response = $this->get('/booking/123');

        $response->assertNotFound();
    }

    public function test_booking_service_rejects_guests_above_villa_capacity(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Kapasitas Test',
            'slug' => 'cabang-kapasitas-test',
            'is_active' => true,
        ]);

        $villaType = VillaType::create([
            'branch_id' => $branch->id,
            'name' => 'Villa Kapasitas Test',
            'slug' => 'villa-kapasitas-test',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'standard_capacity' => 2,
            'max_capacity' => 4,
            'is_active' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Jumlah tamu melebihi kapasitas villa.'
        );

        app(BookingCreationService::class)->create([
            'villa_type_id' => $villaType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guest_count' => 5,
            'customer_name' => 'Pelanggan Test',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
        ]);
       }
}
