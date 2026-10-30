
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->restrictOnDelete();

            $table->foreignId('addon_id')
                ->constrained('addons')
                ->restrictOnDelete();

            // Snapshot informasi layanan saat dipesan
            $table->string('addon_name', 150);
            $table->unsignedBigInteger('unit_price');

            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('nights')->default(1);
            $table->unsignedInteger('persons')->default(1);

            $table->unsignedBigInteger('total_price');

            $table->timestamps();

            $table->index(['booking_id', 'addon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_addons');
    }
};
