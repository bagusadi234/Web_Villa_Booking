
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->restrictOnDelete();

            $table->enum('transaction_type', [
                'dp',
                'full_payment',
                'remaining_payment',
                'refund',
            ]);

            $table->enum('payment_method', [
                'midtrans',
                'xendit',
                'bank_transfer',
                'cash',
                'other',
            ])->default('other');

            $table->unsignedBigInteger('amount');

            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'expired',
                'cancelled',
            ])->default('pending');

            // ID transaksi dari payment gateway atau sistem eksternal
            $table->string('external_transaction_id', 150)->nullable();

            // Referensi pembayaran yang dikembalikan dananya
            $table->foreignId('related_payment_id')
                ->nullable()
                ->constrained('payments')
                ->restrictOnDelete();

            $table->timestamp('paid_at')->nullable();
            $table->json('gateway_response')->nullable();

            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index('external_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
