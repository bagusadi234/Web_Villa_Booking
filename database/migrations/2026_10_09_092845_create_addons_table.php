
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            $table->string('name', 150);
            $table->text('description')->nullable();

            $table->enum('pricing_type', [
                'per_booking',
                'per_night',
                'per_person',
                'per_person_per_night',
            ]);

            $table->unsignedBigInteger('price');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['branch_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
