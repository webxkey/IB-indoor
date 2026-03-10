<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('booking_booking')) {
            return;
        }

        // Create function that sends notifications
        DB::statement(<<<SQL
        CREATE OR REPLACE FUNCTION notify_booking_changes()
        RETURNS TRIGGER AS \$\$
        DECLARE
            notification JSON;
        BEGIN
            IF TG_OP = 'INSERT' THEN
                notification = json_build_object(
                    'action', 'created',
                    'booking_id', NEW.id,
                    'user_name', NEW.user_name,
                    'complex_id', NEW.complex_id_id
                );
            ELSIF TG_OP = 'UPDATE' THEN
                notification = json_build_object(
                    'action', 'updated',
                    'booking_id', NEW.id,
                    'user_name', NEW.user_name,
                    'complex_id', NEW.complex_id_id
                );
            ELSIF TG_OP = 'DELETE' THEN
                notification = json_build_object(
                    'action', 'deleted',
                    'booking_id', OLD.id,
                    'user_name', OLD.user_name,
                    'complex_id', OLD.complex_id_id
                );
            END IF;
            
            PERFORM pg_notify('booking_notification', notification::text);
            
            IF TG_OP = 'DELETE' THEN
                RETURN OLD;
            END IF;
            RETURN NEW;
        END;
        \$\$ LANGUAGE plpgsql;
        SQL);

        // Create the trigger
        DB::statement(<<<SQL
        DROP TRIGGER IF EXISTS booking_notification_trigger ON booking_booking;
        SQL);

        DB::statement(<<<SQL
        CREATE TRIGGER booking_notification_trigger
        AFTER INSERT OR UPDATE OR DELETE ON booking_booking
        FOR EACH ROW
        EXECUTE FUNCTION notify_booking_changes();
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_booking')) {
            DB::statement("DROP TRIGGER IF EXISTS booking_notification_trigger ON booking_booking;");
        }
        DB::statement("DROP FUNCTION IF EXISTS notify_booking_changes();");
    }
};
