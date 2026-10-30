
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_code', 40)->unique();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Snapshot data pelanggan
            $table->string('customer_name', 150);
            $table->string('customer_email', 255);
            $table->string('customer_phone', 30);

            // Periode menginap
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedSmallInteger('guest_count')->default(1);

            // Sumber pemesanan
            $table->enum('source', [
                'website',
                'admin',
                'whatsapp',
                'phone',
                'walk_in',
                'ota',
            ])->default('website');

            // Status booking
            $table->enum('status', [
                'pending_payment',
                'confirmed',
                'checked_in',
                'checked_out',
                'cancelled',
                'expired',
                'no_show',
            ])->default('pending_payment');

            // Status pembayaran
            $table->enum('payment_status', [
                'unpaid',
                'partially_paid',
                'paid',
                'partially_refunded',
                'refunded',
            ])->default('unpaid');

            // Nilai uang dalam rupiah
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('service_charge')->default(0);
            $table->unsignedBigInteger('total_amount')->default(0);

            // Batas waktu sementara untuk booking
            $table->timestamp('hold_expires_at')->nullable();

            // Informasi pembatalan
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->text('special_requests')->nullable();

            $table->timestamps();

            $table->index(['branch_id', 'status']);
            $table->index(['check_in', 'check_out']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'hold_expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
