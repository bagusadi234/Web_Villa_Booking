<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\VillaType;
use App\Models\VillaUnit;
use App\Models\PricingRule;
use App\Models\Payment;
use App\Services\BookingCreationService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createTestBooking()
    {
        $branch = Branch::create([
            'name' => 'Cabang Payment Test',
            'slug' => 'cabang-payment-test',
            'is_active' => true,
        ]);

        $villaType = VillaType::create([
            'branch_id' => $branch->id,
            'name' => 'Villa Payment Test',
            'slug' => 'villa-payment-test',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'standard_capacity' => 2,
            'max_capacity' => 4,
            'is_active' => true,
        ]);

        VillaUnit::create([
            'villa_type_id' => $villaType->id,
            'unit_code' => 'PAY-01',
            'unit_name' => 'Unit Payment Test',
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

        $booking = app(BookingCreationService::class)->create([
            'villa_type_id' => $villaType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guest_count' => 2,
            'customer_name' => 'Pelanggan Payment Test',
            'customer_email' => 'payment@example.com',
            'customer_phone' => '081234567890',
        ]);

        // Pastikan total booking diketahui oleh tes.
        $booking->update(['total_amount' => 1000000]);

        return $booking->fresh();
    }

    public function test_valid_dp_creates_pending_payment(): void
    {
        $booking = $this->createTestBooking();

        $payment = app(PaymentService::class)->createPayment(
            $booking->id,
            'dp',
            300000
        );

        $this->assertSame('dp', $payment->transaction_type);
        $this->assertSame(300000, (int) $payment->amount);
        $this->assertSame('pending', $payment->status);

        // Membuat transaksi belum berarti pembayaran berhasil.
        $this->assertSame('unpaid', $booking->fresh()->payment_status);
    }

    public function test_full_payment_must_equal_total_amount(): void
    {
        $booking = $this->createTestBooking();

        $this->expectException(InvalidArgumentException::class);

        app(PaymentService::class)->createPayment(
            $booking->id,
            'full_payment',
            500000
        );
    }

    public function test_payment_amount_cannot_exceed_total(): void
    {
        $booking = $this->createTestBooking();

        $this->expectException(InvalidArgumentException::class);

        app(PaymentService::class)->createPayment(
            $booking->id,
            'dp',
            1200000
        );
    }

    public function test_expired_booking_cannot_create_payment(): void
    {
        $booking = $this->createTestBooking();

        $booking->update([
            'hold_expires_at' => now()->subMinute(),
        ]);

        $this->expectException(ValidationException::class);

        app(PaymentService::class)->createPayment(
            $booking->id,
            'dp',
            300000
        );
    }

    public function test_second_pending_payment_is_rejected(): void
    {
        $booking = $this->createTestBooking();

        Payment::create([
            'booking_id' => $booking->id,
            'transaction_type' => 'dp',
            'payment_method' => 'other',
            'amount' => 300000,
            'status' => 'pending',
        ]);

        $this->expectException(ValidationException::class);

        app(PaymentService::class)->createPayment(
            $booking->id,
            'full_payment',
            1000000
        );
    }

    public function test_verified_full_payment_confirms_active_booking(): void
    {
        $booking = $this->createTestBooking();

        $payment = app(PaymentService::class)->createPayment(
            $booking->id,
            'full_payment',
            1000000
        );

        $confirmedPayment = app(PaymentService::class)
            ->confirmVerifiedPayment(
                $payment->id,
                1000000,
                'TEST-TRANSACTION-001'
            );

        $this->assertSame('success', $confirmedPayment->status);
        $this->assertNotNull($confirmedPayment->paid_at);
        $this->assertSame(
            'TEST-TRANSACTION-001',
            $confirmedPayment->external_transaction_id
        );

        $booking->refresh();

        $this->assertSame('paid', $booking->payment_status);
        $this->assertSame('confirmed', $booking->status);
    }

    public function test_verified_amount_mismatch_is_rejected(): void
    {
        $booking = $this->createTestBooking();

        $payment = app(PaymentService::class)->createPayment(
            $booking->id,
            'full_payment',
            1000000
        );

        try {
            app(PaymentService::class)->confirmVerifiedPayment(
                $payment->id,
                900000
            );

            $this->fail('Nominal yang tidak cocok seharusnya ditolak.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                'pending',
                $payment->fresh()->status
            );

            $this->assertSame(
                'unpaid',
                $booking->fresh()->payment_status
            );
        }
    }

    public function test_repeated_confirmation_does_not_duplicate_processing(): void
    {
        $booking = $this->createTestBooking();

        $payment = app(PaymentService::class)->createPayment(
            $booking->id,
            'full_payment',
            1000000
        );

        $service = app(PaymentService::class);

        $firstResult = $service->confirmVerifiedPayment(
            $payment->id,
            1000000,
            'TEST-TRANSACTION-002'
        );

        $secondResult = $service->confirmVerifiedPayment(
            $payment->id,
            1000000,
            'TEST-TRANSACTION-002'
        );

        $this->assertSame('success', $firstResult->status);
        $this->assertSame('success', $secondResult->status);

        $this->assertSame(
            1,
            Booking::query()
                ->whereKey($booking->id)
                ->where('status', 'confirmed')
                ->count()
        );

        $this->assertSame(
            1,
            \App\Models\BookingStatusHistory::query()
                ->where('booking_id', $booking->id)
                ->where('to_status', 'confirmed')
                ->count()
        );
    }

    public function test_payment_after_hold_expires_does_not_confirm_booking(): void
    {
        $booking = $this->createTestBooking();

        $payment = app(PaymentService::class)->createPayment(
            $booking->id,
            'full_payment',
            1000000
        );

        // Simulasikan pembayaran terverifikasi setelah masa hold berakhir.
        $booking->update([
            'hold_expires_at' => now()->subMinute(),
        ]);

        $result = app(PaymentService::class)->confirmVerifiedPayment(
            $payment->id,
            1000000,
            'TEST-TRANSACTION-003'
        );

        $this->assertSame('success', $result->status);
        $this->assertSame(
            'paid',
            $booking->fresh()->payment_status
        );
        $this->assertSame(
            'pending_payment',
            $booking->fresh()->status
        );
    }

        
}
