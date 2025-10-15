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
            // Add complex_id column after id column
            $table->foreignId('complex_id')
                  ->after('id')
                  ->nullable()
                  ->constrained('complexes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_venue', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['complex_id']);
            // Then drop the column
            $table->dropColumn('complex_id');
        });
    }
};