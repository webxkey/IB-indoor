<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('booking_booking') && !Schema::hasColumn('booking_booking', 'complex_id_id')) {
            Schema::table('booking_booking', function (Blueprint $table) {
                $table->unsignedBigInteger('complex_id_id')->nullable();
                $table->index('complex_id_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('booking_booking') && Schema::hasColumn('booking_booking', 'complex_id_id')) {
            Schema::table('booking_booking', function (Blueprint $table) {
                $table->dropIndex(['complex_id_id']);
                $table->dropColumn('complex_id_id');
            });
        }
    }
};
