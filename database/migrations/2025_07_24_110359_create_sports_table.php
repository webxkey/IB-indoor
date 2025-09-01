<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for sports table.
     * Stores all sports/games offered at complexes with pricing and availability.
     */
    public function up()
    {
        Schema::create('sports', function (Blueprint $table) {
            // Primary key
            $table->id('game_id');
            
            // Relationship
            $table->foreignId('complex_id')->constrained('complexes')->onDelete('cascade')
                  ->comment('Reference to parent complex');
            
            // Sport details
            $table->string('game_name')->comment('Name of the sport (e.g., Tennis, Futsal)');
            $table->string('game_type')->comment('Indoor / Outdoor / Both');
            
            // Pricing
            $table->string('rate_type')->comment('Per hour, per day, per session');
            $table->decimal('price', 10, 2)->comment('Base price per rate unit');
            
            // Availability
            $table->integer('maximum_court')->comment('Total courts/fields available');
            
            // Status
            $table->enum('status', ['Active', 'Inactive'])->default('Active')
                  ->comment('Whether sport is currently available');
            
            // Media
            $table->string('game_image')->nullable()->comment('Image representing the sport');
            
            // Descriptive content
            $table->text('description')->nullable()->comment('Brief description of sport');
            
            // Additional info
            $table->json('additional_charges')->nullable()
                  ->comment('JSON of optional fees (lighting, equipment)');
            $table->boolean('advance_required')->default(false)
                  ->comment('Whether advance booking payment is needed');
            
            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sports');
    }
};