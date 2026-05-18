<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS booking_after_insert_trigger ON booking_booking');
        DB::statement('DROP TRIGGER IF EXISTS booking_after_update_trigger ON booking_booking');
        DB::statement('DROP FUNCTION IF EXISTS booking_after_insert_queue_fn()');
        DB::statement('DROP FUNCTION IF EXISTS booking_after_update_queue_fn()');
        Schema::dropIfExists('booking_change_queue');
    }

    public function down(): void
    {
        // No-op: the replaced approach was the database-trigger polling design
        // that has been removed in favor of Reverb broadcast events.
    }
};
