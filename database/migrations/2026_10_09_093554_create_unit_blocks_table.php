
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('villa_unit_id')
                ->constrained('villa_units')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->enum('reason', [
                'maintenance',
                'renovation',
                'damaged',
                'owner_use',
                'other',
            ])->default('maintenance');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['villa_unit_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_blocks');
    }
};
