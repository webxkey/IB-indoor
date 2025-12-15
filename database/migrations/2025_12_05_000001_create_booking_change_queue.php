<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create a queue table for database changes
        Schema::create('booking_change_queue', function (Blueprint $table) {
            $table->id();
            $table->string('action', 20); // 'INSERT', 'UPDATE', 'DELETE'
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('complex_id');
            $table->json('data')->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['processed', 'created_at']);
            $table->index('complex_id');
        });

        // Create triggers to capture ALL database changes
        DB::unprepared('
            DROP TRIGGER IF EXISTS booking_after_insert_trigger;
            
            CREATE TRIGGER booking_after_insert_trigger
            AFTER INSERT ON booking_booking
            FOR EACH ROW
            BEGIN
                INSERT INTO booking_change_queue (action, booking_id, complex_id, data, processed, created_at)
                VALUES (
                    "INSERT",
                    NEW.id,
                    NEW.complex_id_id,
                    JSON_OBJECT(
                        "id", NEW.id,
                        "user_name", NEW.user_name,
                        "game_name", NEW.game_name,
                        "court_number", NEW.court_number,
                        "start_time", NEW.start_time,
                        "end_time", NEW.end_time,
                        "status", NEW.status,
                        "booking_date", NEW.booking_date
                    ),
                    FALSE,
                    NOW()
                );
            END;
        ');

        DB::unprepared('
            DROP TRIGGER IF EXISTS booking_after_update_trigger;
            
            CREATE TRIGGER booking_after_update_trigger
            AFTER UPDATE ON booking_booking
            FOR EACH ROW
            BEGIN
                -- Only track if important fields changed
                IF OLD.status != NEW.status 
                   OR OLD.start_time != NEW.start_time 
                   OR OLD.end_time != NEW.end_time 
                   OR OLD.court_number != NEW.court_number THEN
                    
                    INSERT INTO booking_change_queue (action, booking_id, complex_id, data, processed, created_at)
                    VALUES (
                        "UPDATE",
                        NEW.id,
                        NEW.complex_id_id,
                        JSON_OBJECT(
                            "id", NEW.id,
                            "user_name", NEW.user_name,
                            "game_name", NEW.game_name,
                            "court_number", NEW.court_number,
                            "start_time", NEW.start_time,
                            "end_time", NEW.end_time,
                            "status", NEW.status,
                            "booking_date", NEW.booking_date
                        ),
                        FALSE,
                        NOW()
                    );
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS booking_after_insert_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS booking_after_update_trigger');
        Schema::dropIfExists('booking_change_queue');
    }
};
