
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villa_type_facility', function (Blueprint $table) {
            $table->id();

            $table->foreignId('villa_type_id')
                ->constrained('villa_types')
                ->cascadeOnDelete();

            $table->foreignId('facility_id')
                ->constrained('facilities')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['villa_type_id', 'facility_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villa_type_facility');
    }
};
