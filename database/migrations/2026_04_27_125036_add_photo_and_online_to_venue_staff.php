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
        Schema::table('venue_staff', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('email');
            $table->boolean('is_online')->default(false)->after('status');
            $table->string('status_detail')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venue_staff', function (Blueprint $table) {
            $table->dropColumn(['photo', 'is_online']);
        });
    }
};
