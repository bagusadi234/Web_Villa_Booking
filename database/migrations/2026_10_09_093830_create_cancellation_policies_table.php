
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->restrictOnDelete();

            $table->string('name', 150);

            // Minimal jumlah hari sebelum check-in
            $table->unsignedSmallInteger('min_days_before_checkin')
                ->default(7);

            // Persentase refund dan biaya administrasi
            $table->unsignedTinyInteger('refund_percentage')
                ->default(90);

            $table->unsignedTinyInteger('admin_fee_percentage')
                ->default(10);

            $table->boolean('allow_reschedule')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['branch_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellation_policies');
    }
};
