<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for sports complexes table.
     * Stores all sports complex information including location, contact, and amenities.
     */
    public function up()
    {
        Schema::create('complexes', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Basic information
            $table->string('complex_name')->comment('Name of the sports complex');
            $table->string('complex_type')->comment('Indoor, Outdoor, Mixed facility type');
            
            // Location details
            $table->string('county')->comment('Administrative area or district');
            $table->string('location')->comment('GPS coordinates or location name');
            $table->text('address')->comment('Full physical address');
            $table->string('postal_code')->comment('Postal/ZIP code');
            
            // Contact information
            $table->string('contact_number')->comment('Main phone number');
            $table->string('email_address')->comment('Official email address');
            $table->string('website')->nullable()->comment('Website or landing page URL');
            
            // Status and operational details
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->comment('Complex operational status');
            $table->json('opening_hours')->nullable()->comment('JSON of daily opening hours (Mon-Sun)');
            
            // Facility features
            $table->json('amenities')->nullable()->comment('JSON array of amenities: Wi-Fi, Parking, etc.');
            
            // Media
            $table->string('cover_image')->nullable()->comment('URL to hero/main image');
            $table->json('gallery_images')->nullable()->comment('JSON array of additional image URLs');
            $table->string('video_tour_url')->nullable()->comment('YouTube/Vimeo tour URL');
            
            // Descriptive content
            $table->text('description')->nullable()->comment('Detailed description of complex');
            $table->text('terms')->nullable()->comment('Terms and conditions');
            
            // Social media
            $table->json('social_links')->nullable()->comment('JSON of social media links');
            
            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complexes');
    }
};