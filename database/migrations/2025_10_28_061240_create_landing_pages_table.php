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
        if (!Schema::hasTable('landing_pages')) {
            Schema::create('landing_pages', function (Blueprint $table) {
                $table->id(); // bigint unsigned primary key
                $table->string('page_name');
                $table->string('section_title')->nullable();
                $table->text('section_description')->nullable();
                $table->longText('images')->nullable(); // JSON stored as text
                $table->integer('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps(); // created_at and updated_at
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
