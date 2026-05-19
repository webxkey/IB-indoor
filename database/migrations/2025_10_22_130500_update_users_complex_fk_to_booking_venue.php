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
        // If the complex_id column exists, drop any existing FK constraints and add the new one
        if (Schema::hasColumn('users', 'complex_id')) {
            // Find any foreign key constraints for users.complex_id from information_schema
            $dbName = DB::getDatabaseName();
            $constraints = DB::select(
                'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
                [$dbName, 'users', 'complex_id']
            );

            foreach ($constraints as $c) {
                $constraintName = $c->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE `users` DROP FOREIGN KEY `{$constraintName}`");
                } catch (\Exception $e) {
                    // ignore
                }
            }

            // Now add the foreign key to booking_venue if not present
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('complex_id')->references('id')->on('booking_venue')->nullOnDelete();
                });
            } catch (\Exception $e) {
                // ignore any error adding the FK
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'complex_id')) {
            $dbName = DB::getDatabaseName();
            $constraints = DB::select(
                'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
                [$dbName, 'users', 'complex_id']
            );
            foreach ($constraints as $c) {
                $constraintName = $c->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE `users` DROP FOREIGN KEY `{$constraintName}`");
                } catch (\Exception $e) {
                    // ignore
                }
            }

            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('complex_id')->references('id')->on('complexes')->nullOnDelete();
                });
            } catch (\Exception $e) {
                // ignore
            }
        }
    }
};
