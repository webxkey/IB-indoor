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
        Schema::table('booking_venue', function (Blueprint $table) {
            $table->string('geohash', 20)->nullable()->change();
            $table->string('image_url', 500)->nullable()->change();
            $table->string('cover_image', 500)->nullable()->change();
            $table->string('video_tour_url', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_venue', function (Blueprint $table) {
            $table->string('geohash', 8)->nullable()->change();
            $table->string('image_url', 200)->nullable()->change();
            $table->string('cover_image', 200)->nullable()->change();
            $table->string('video_tour_url', 200)->nullable()->change();
        });
    }
};
