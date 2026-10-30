
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            $table->foreignId('villa_unit_id')
                ->constrained('villa_units')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('guest_count')->default(1);

            // Harga dasar unit selama periode menginap
            $table->unsignedBigInteger('subtotal')->default(0);

            $table->timestamps();

            $table->unique(['booking_id', 'villa_unit_id']);
            $table->index('villa_unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_units');
    }
};
