
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('villa_type_id')
                ->constrained('villa_types')
                ->restrictOnDelete();

            $table->enum('rule_type', [
                'weekday',
                'weekend',
                'high_season',
                'special_date',
            ]);

            $table->unsignedBigInteger('price');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'villa_type_id',
                'rule_type',
                'is_active',
            ]);

            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
