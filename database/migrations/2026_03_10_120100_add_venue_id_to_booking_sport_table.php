<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('booking_sport') && !Schema::hasColumn('booking_sport', 'venue_id')) {
            Schema::table('booking_sport', function (Blueprint $table) {
                $table->unsignedBigInteger('venue_id')->nullable();
                $table->index('venue_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('booking_sport') && Schema::hasColumn('booking_sport', 'venue_id')) {
            Schema::table('booking_sport', function (Blueprint $table) {
                $table->dropIndex(['venue_id']);
                $table->dropColumn('venue_id');
            });
        }
    }
};
