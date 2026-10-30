<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('public_token', 64)
                ->nullable()
                ->after('booking_code');
        });

        // Beri token pada booking lama agar tetap dapat diakses.
        DB::table('bookings')
            ->whereNull('public_token')
            ->orderBy('id')
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    do {
                        $token = Str::random(64);
                    } while (
                        DB::table('bookings')
                            ->where('public_token', $token)
                            ->exists()
                    );

                    DB::table('bookings')
                        ->where('id', $booking->id)
                        ->update(['public_token' => $token]);
                }
            });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unique('public_token');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
