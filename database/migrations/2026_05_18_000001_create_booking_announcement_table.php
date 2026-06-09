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
        if (!Schema::hasTable('booking_announcement')) {
            Schema::create('booking_announcement', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('venue_id')->nullable();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->text('short_description')->nullable();
                $table->text('full_description')->nullable();
                $table->string('image')->nullable();
                $table->string('fallback_bg_color')->nullable();
                $table->string('label')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_pinned')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamp('expires_at')->nullable();
                $table->string('read_more_label')->nullable();
                $table->string('read_more_url')->nullable();
                $table->timestamps();

                $table->foreign('venue_id')->references('id')->on('booking_venue')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_announcement');
    }
};
