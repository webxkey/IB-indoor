<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_sport', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_sport', 'private_booking_enabled')) {
                $table->boolean('private_booking_enabled')->default(true)->comment('Allow customers to make private bookings for this sport when pricing is configured.');
            }
        });

        Schema::table('pools_pool', function (Blueprint $table) {
            if (!Schema::hasColumn('pools_pool', 'private_booking_enabled')) {
                $table->boolean('private_booking_enabled')->default(true)->comment('Allow customers to make private bookings for this pool when pricing is configured.');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_sport', function (Blueprint $table) {
            if (Schema::hasColumn('booking_sport', 'private_booking_enabled')) {
                $table->dropColumn('private_booking_enabled');
            }
        });

        Schema::table('pools_pool', function (Blueprint $table) {
            if (Schema::hasColumn('pools_pool', 'private_booking_enabled')) {
                $table->dropColumn('private_booking_enabled');
            }
        });
    }
};
