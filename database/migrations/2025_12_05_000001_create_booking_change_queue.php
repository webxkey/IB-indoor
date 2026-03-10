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

        // PostgreSQL-compatible trigger functions + triggers
        if (Schema::hasTable('booking_booking')) {
            DB::statement('DROP TRIGGER IF EXISTS booking_after_insert_trigger ON booking_booking;');
            DB::statement('DROP TRIGGER IF EXISTS booking_after_update_trigger ON booking_booking;');
            DB::statement('DROP FUNCTION IF EXISTS booking_after_insert_queue_fn();');
            DB::statement('DROP FUNCTION IF EXISTS booking_after_update_queue_fn();');

            DB::statement(<<<SQL
            CREATE FUNCTION booking_after_insert_queue_fn()
            RETURNS TRIGGER AS $$
            BEGIN
                INSERT INTO booking_change_queue (action, booking_id, complex_id, data, processed, created_at)
                VALUES (
                    'INSERT',
                    NEW.id,
                    NEW.complex_id_id,
                    json_build_object(
                        'id', NEW.id,
                        'user_name', NEW.user_name,
                        'game_name', NEW.game_name,
                        'court_number', NEW.court_number,
                        'start_time', NEW.start_time,
                        'end_time', NEW.end_time,
                        'status', NEW.status,
                        'booking_date', NEW.booking_date
                    ),
                    FALSE,
                    NOW()
                );

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
            SQL);

            DB::statement(<<<SQL
            CREATE TRIGGER booking_after_insert_trigger
            AFTER INSERT ON booking_booking
            FOR EACH ROW
            EXECUTE FUNCTION booking_after_insert_queue_fn();
            SQL);

            DB::statement(<<<SQL
            CREATE FUNCTION booking_after_update_queue_fn()
            RETURNS TRIGGER AS $$
            BEGIN
                IF OLD.status IS DISTINCT FROM NEW.status
                   OR OLD.start_time IS DISTINCT FROM NEW.start_time
                   OR OLD.end_time IS DISTINCT FROM NEW.end_time
                   OR OLD.court_number IS DISTINCT FROM NEW.court_number THEN

                    INSERT INTO booking_change_queue (action, booking_id, complex_id, data, processed, created_at)
                    VALUES (
                        'UPDATE',
                        NEW.id,
                        NEW.complex_id_id,
                        json_build_object(
                            'id', NEW.id,
                            'user_name', NEW.user_name,
                            'game_name', NEW.game_name,
                            'court_number', NEW.court_number,
                            'start_time', NEW.start_time,
                            'end_time', NEW.end_time,
                            'status', NEW.status,
                            'booking_date', NEW.booking_date
                        ),
                        FALSE,
                        NOW()
                    );
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
            SQL);

            DB::statement(<<<SQL
            CREATE TRIGGER booking_after_update_trigger
            AFTER UPDATE ON booking_booking
            FOR EACH ROW
            EXECUTE FUNCTION booking_after_update_queue_fn();
            SQL);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_booking')) {
            DB::statement('DROP TRIGGER IF EXISTS booking_after_insert_trigger ON booking_booking;');
            DB::statement('DROP TRIGGER IF EXISTS booking_after_update_trigger ON booking_booking;');
        }
        DB::statement('DROP FUNCTION IF EXISTS booking_after_insert_queue_fn();');
        DB::statement('DROP FUNCTION IF EXISTS booking_after_update_queue_fn();');
        Schema::dropIfExists('booking_change_queue');
    }
};
