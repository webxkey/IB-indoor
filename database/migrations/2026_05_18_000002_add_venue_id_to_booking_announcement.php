<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_announcement', function (Blueprint $table) {
            // Nullable: existing rows are global (created by Django for the app);
            // venue-scoped rows created from /staff/announcements get a venue_id.
            $table->unsignedBigInteger('venue_id')->nullable()->after('id');
            $table->foreign('venue_id')->references('id')->on('booking_venue')->nullOnDelete();
            $table->index('venue_id');
        });
    }

    public function down(): void
    {
        Schema::table('booking_announcement', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->dropIndex(['venue_id']);
            $table->dropColumn('venue_id');
        });
    }
};
