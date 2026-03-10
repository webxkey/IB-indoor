<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('booking_booking')) {
            return;
        }

        Schema::table('booking_booking', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_booking', 'user_id_id')) {
                $table->unsignedBigInteger('user_id_id')->nullable();
                $table->index('user_id_id');
            }

            if (!Schema::hasColumn('booking_booking', 'game_id_id')) {
                $table->unsignedBigInteger('game_id_id')->nullable();
                $table->index('game_id_id');
            }
        });

        if (Schema::hasColumn('booking_booking', 'user_id') && Schema::hasColumn('booking_booking', 'user_id_id')) {
            DB::statement('UPDATE booking_booking SET user_id_id = user_id WHERE user_id_id IS NULL AND user_id IS NOT NULL');
        }

        if (Schema::hasColumn('booking_booking', 'game_id') && Schema::hasColumn('booking_booking', 'game_id_id')) {
            DB::statement('UPDATE booking_booking SET game_id_id = game_id WHERE game_id_id IS NULL AND game_id IS NOT NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('booking_booking')) {
            return;
        }

        Schema::table('booking_booking', function (Blueprint $table) {
            if (Schema::hasColumn('booking_booking', 'user_id_id')) {
                $table->dropIndex(['user_id_id']);
                $table->dropColumn('user_id_id');
            }

            if (Schema::hasColumn('booking_booking', 'game_id_id')) {
                $table->dropIndex(['game_id_id']);
                $table->dropColumn('game_id_id');
            }
        });
    }
};
