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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'complex_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('complex_id')->nullable();
                $table->index('complex_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'complex_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['complex_id']);
                $table->dropColumn('complex_id');
            });
        }
    }
};
