<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // booking_discount
        Schema::create('booking_discount', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100);
            $table->string('color', 20);
            $table->unsignedBigInteger('sport_id');
        });

        // booking_facility
        Schema::create('booking_facility', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('icon', 50);
            $table->unsignedBigInteger('sport_id');
        });

        // booking_galleryimage
        Schema::create('booking_galleryimage', function (Blueprint $table) {
            $table->id();
            $table->string('image_url', 200);
            $table->unsignedBigInteger('venue_id');
        });

        // booking_notification
        Schema::create('booking_notification', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('title', 255);
            $table->longText('message');
            $table->json('data'); // CHECK (json_valid)
            $table->boolean('is_read')->default(0);
            $table->dateTime('created_at', 6);
            $table->unsignedBigInteger('user_id');
        });

        // booking_sport
        Schema::create('booking_sport', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('price', 10, 2);
            $table->string('image', 200);
            $table->boolean('available')->default(1);
            $table->string('game_type', 255)->nullable();
            $table->string('rate_type', 255)->nullable();
            $table->integer('maximum_court')->nullable();
            $table->string('status', 10);
            $table->longText('description')->nullable();
            $table->json('additional_charges')->nullable();
            $table->boolean('advance_required')->default(0);
            $table->dateTime('created_at', 6);
            $table->dateTime('updated_at', 6);
            $table->unsignedBigInteger('venue_id');
            $table->double('average_rating');
        });

        // booking_sportreview
        Schema::create('booking_sportreview', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('rating'); // check >=0
            $table->longText('comment');
            $table->date('visit_date')->nullable();
            $table->json('photos');
            $table->boolean('would_recommend');
            $table->dateTime('created_at', 6);
            $table->dateTime('updated_at', 6);
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('sport_id');
            $table->unsignedBigInteger('user_id');
            $table->json('categories')->nullable();
        });

        // booking_venue
        Schema::create('booking_venue', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->longText('address');
            $table->double('rating');
            $table->integer('reviews');
            $table->string('image_url', 200);
            $table->string('complex_type', 255)->nullable();
            $table->string('county', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->string('email_address', 254)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('status', 10);
            $table->json('opening_hours')->nullable();
            $table->json('amenities')->nullable();
            $table->string('cover_image', 200)->nullable();
            $table->json('gallery_images_json')->nullable();
            $table->string('video_tour_url', 200)->nullable();
            $table->longText('description')->nullable();
            $table->longText('terms')->nullable();
            $table->json('social_links')->nullable();
            $table->dateTime('created_at', 6);
            $table->dateTime('updated_at', 6);
        });

        // booking_venuereview
        Schema::create('booking_venuereview', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('rating'); // check >=0
            $table->longText('comment');
            $table->json('categories')->nullable();
            $table->json('photos')->nullable();
            $table->boolean('would_recommend');
            $table->dateTime('created_at', 6);
            $table->dateTime('updated_at', 6);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('venue_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_venuereview');
        Schema::dropIfExists('booking_venue');
        Schema::dropIfExists('booking_sportreview');
        Schema::dropIfExists('booking_sport');
        Schema::dropIfExists('booking_notification');
        Schema::dropIfExists('booking_galleryimage');
        Schema::dropIfExists('booking_facility');
        Schema::dropIfExists('booking_discount');
    }
};
