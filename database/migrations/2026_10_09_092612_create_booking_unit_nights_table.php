
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_unit_nights', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_unit_id')
                ->constrained('booking_units')
                ->cascadeOnDelete();

            $table->date('stay_date');

            // Snapshot harga satu malam dalam rupiah
            $table->unsignedBigInteger('nightly_price');

            // Jenis aturan harga yang digunakan
            $table->enum('price_type', [
                'weekday',
                'weekend',
                'high_season',
                'special_date',
                'manual',
            ])->default('weekday');

            $table->timestamps();

            $table->unique(['booking_unit_id', 'stay_date']);
            $table->index('stay_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_unit_nights');
    }
};
