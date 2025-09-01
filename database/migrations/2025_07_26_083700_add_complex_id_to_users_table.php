<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('complex_id')
                  ->nullable()
                  ->after('profile_photo_path') // place it logically
                  ->constrained('complexes')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['complex_id']);
            $table->dropColumn('complex_id');
        });
    }
};
