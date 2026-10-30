
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villa_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('villa_type_id')
                ->constrained('villa_types')
                ->cascadeOnDelete();

            $table->string('image_path', 255);
            $table->string('alt_text', 200)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['villa_type_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villa_images');
    }
};
