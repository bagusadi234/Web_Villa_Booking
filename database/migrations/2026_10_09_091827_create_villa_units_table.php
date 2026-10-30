
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villa_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('villa_type_id')
                ->constrained('villa_types')
                ->restrictOnDelete();

            $table->string('unit_code', 50);
            $table->string('unit_name', 150)->nullable();

            $table->enum('condition_status', [
                'good',
                'maintenance',
                'out_of_service',
            ])->default('good');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['villa_type_id', 'unit_code']);
            $table->index([
                'villa_type_id',
                'condition_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villa_units');
    }
};
