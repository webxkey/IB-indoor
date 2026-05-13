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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'two_step_enabled')) {
                $table->boolean('two_step_enabled')->default(false);
            }

            if (!Schema::hasColumn('users', 'two_step_method')) {
                $table->enum('two_step_method', ['email', 'phone'])->default('email')->nullable();
            }

            if (!Schema::hasColumn('users', 'email_verified')) {
                $table->boolean('email_verified')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'two_step_enabled')) {
                $table->dropColumn('two_step_enabled');
            }

            if (Schema::hasColumn('users', 'two_step_method')) {
                $table->dropColumn('two_step_method');
            }

            if (Schema::hasColumn('users', 'email_verified')) {
                $table->dropColumn('email_verified');
            }
        });
    }
};
