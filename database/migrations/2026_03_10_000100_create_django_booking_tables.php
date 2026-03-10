<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('booking_sport')) {
            Schema::create('booking_sport', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('venue_id')->nullable();
                $table->string('name');
                $table->decimal('price', 10, 2)->default(0);
                $table->string('image')->nullable();
                $table->boolean('available')->default(true);
                $table->string('game_type', 50)->nullable();
                $table->string('rate_type', 50)->nullable();
                $table->integer('maximum_court')->default(1);
                $table->string('status', 30)->default('active');
                $table->text('description')->nullable();
                $table->json('additional_charges')->nullable();
                $table->boolean('advance_required')->default(false);
                $table->float('average_rating')->default(0);
                $table->json('opening_hours')->nullable();
                $table->json('blocked_slots')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_venue')) {
            Schema::create('booking_venue', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('address')->nullable();
                $table->float('rating')->default(0);
                $table->integer('reviews')->default(0);
                $table->string('image_url')->nullable();
                $table->string('complex_type', 50)->nullable();
                $table->string('county', 100)->nullable();
                $table->string('location')->nullable();
                $table->string('postal_code', 30)->nullable();
                $table->string('contact_number', 30)->nullable();
                $table->string('email_address')->nullable();
                $table->string('website')->nullable();
                $table->string('status', 30)->default('active');
                $table->json('opening_hours')->nullable();
                $table->json('amenities')->nullable();
                $table->string('cover_image')->nullable();
                $table->json('gallery_images_json')->nullable();
                $table->string('video_tour_url')->nullable();
                $table->text('description')->nullable();
                $table->text('terms')->nullable();
                $table->json('social_links')->nullable();
                $table->json('blocked_slots')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_team')) {
            Schema::create('booking_team', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('location')->nullable();
                $table->json('availability')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_number', 30)->nullable();
                $table->string('cover_image')->nullable();
                $table->date('founded_date')->nullable();
                $table->boolean('is_public')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_chat')) {
            Schema::create('booking_chat', function (Blueprint $table) {
                $table->id();
                $table->string('chat_type', 30)->default('team_group');
                $table->string('name')->nullable();
                $table->unsignedBigInteger('team_id')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->boolean('admin_only')->default(false);
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->index(['chat_type', 'team_id']);
                $table->index(['updated_at']);
            });
        }

        if (!Schema::hasTable('booking_chat_user')) {
            Schema::create('booking_chat_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chat_id');
                $table->unsignedBigInteger('user_id');
                $table->unique(['chat_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('booking_chat_admins')) {
            Schema::create('booking_chat_admins', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chat_id');
                $table->unsignedBigInteger('user_id');
                $table->unique(['chat_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('booking_chatmessage')) {
            Schema::create('booking_chatmessage', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chat_id');
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->unsignedBigInteger('booking_id')->nullable();
                $table->text('message')->nullable();
                $table->string('message_type', 30)->default('text');
                $table->string('file_url')->nullable();
                $table->boolean('is_pinned')->default(false);
                $table->integer('duration')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('media_expires_at')->nullable();
                $table->boolean('is_deleted')->default(false);
                $table->timestamp('deleted_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index('media_expires_at');
            });
        }

        if (!Schema::hasTable('booking_chatreadreceipt')) {
            Schema::create('booking_chatreadreceipt', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chat_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamp('last_read_at')->nullable();
                $table->unique(['chat_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('booking_teamchannelfollower')) {
            Schema::create('booking_teamchannelfollower', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('team_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamp('followed_at')->nullable();
                $table->unique(['team_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('booking_chatmediadownload')) {
            Schema::create('booking_chatmediadownload', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('message_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamp('downloaded_at')->nullable();
                $table->unique(['message_id', 'user_id']);
                $table->index(['message_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('booking_notification')) {
            Schema::create('booking_notification', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('type', 80);
                $table->string('title');
                $table->text('message');
                $table->json('data')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('booking_permanentbooking')) {
            Schema::create('booking_permanentbooking', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('sport_id');
                $table->unsignedBigInteger('team_id')->nullable();
                $table->unsignedBigInteger('chat_id')->nullable();
                $table->json('recurring_config')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->integer('duration')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('booking_booking')) {
            Schema::create('booking_booking', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('user_id_id')->nullable();
                $table->unsignedBigInteger('game_id')->nullable();
                $table->unsignedBigInteger('game_id_id')->nullable();
                $table->unsignedBigInteger('complex_id_id')->nullable();
                $table->unsignedBigInteger('team_id')->nullable();
                $table->unsignedBigInteger('permanent_source_id')->nullable();
                $table->unsignedBigInteger('opponent_team_id')->nullable();
                $table->string('game_name')->nullable();
                $table->string('user_name')->nullable();
                $table->string('user_number', 30)->nullable();
                $table->string('court_number', 30)->nullable();
                $table->date('booking_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->integer('duration')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('payment_status', 30)->default('Pending');
                $table->string('payment_method', 30)->nullable();
                $table->string('status', 30)->default('Confirmed');
                $table->boolean('is_challenge_booking')->default(false);
                $table->text('notes')->nullable();
                $table->string('qr_code')->nullable();
                $table->text('admin_comments')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_facility')) {
            Schema::create('booking_facility', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sport_id');
                $table->string('name');
                $table->string('icon')->nullable();
            });
        }

        if (!Schema::hasTable('booking_discount')) {
            Schema::create('booking_discount', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sport_id');
                $table->string('type', 50);
                $table->string('color', 30)->nullable();
            });
        }

        if (!Schema::hasTable('booking_sportreview')) {
            Schema::create('booking_sportreview', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id')->nullable();
                $table->unsignedBigInteger('sport_id');
                $table->unsignedBigInteger('user_id');
                $table->integer('rating')->default(0);
                $table->text('comment')->nullable();
                $table->date('visit_date')->nullable();
                $table->json('categories')->nullable();
                $table->boolean('would_recommend')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_sportreviewphoto')) {
            Schema::create('booking_sportreviewphoto', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('review_id');
                $table->string('image');
                $table->timestamp('uploaded_at')->nullable();
            });
        }

        if (!Schema::hasTable('booking_venuereview')) {
            Schema::create('booking_venuereview', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('venue_id');
                $table->unsignedBigInteger('user_id');
                $table->integer('rating')->default(0);
                $table->text('comment')->nullable();
                $table->date('visit_date')->nullable();
                $table->json('categories')->nullable();
                $table->boolean('would_recommend')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_venuereviewphoto')) {
            Schema::create('booking_venuereviewphoto', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('review_id');
                $table->string('image');
                $table->timestamp('uploaded_at')->nullable();
            });
        }

        if (!Schema::hasTable('booking_teamchallenge')) {
            Schema::create('booking_teamchallenge', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id')->nullable();
                $table->unsignedBigInteger('challenger_id');
                $table->unsignedBigInteger('challenged_id');
                $table->unsignedBigInteger('sport_id');
                $table->unsignedBigInteger('venue_id')->nullable();
                $table->unsignedBigInteger('chat_room_id')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->unsignedBigInteger('permanent_booking_id')->nullable();
                $table->date('match_date')->nullable();
                $table->time('match_time')->nullable();
                $table->time('match_end_time')->nullable();
                $table->string('court_number', 30)->nullable();
                $table->integer('duration')->nullable()->default(60);
                $table->decimal('stake', 10, 2)->default(0);
                $table->string('status', 30)->default('pending');
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_permanent')->default(false);
                $table->json('recurring_config')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_teamchallengeaction')) {
            Schema::create('booking_teamchallengeaction', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('challenge_id');
                $table->unsignedBigInteger('user_id');
                $table->string('action', 30);
                $table->text('note')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('booking_teamrivalry')) {
            Schema::create('booking_teamrivalry', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('team_a_id');
                $table->unsignedBigInteger('team_b_id');
                $table->unsignedBigInteger('chat_id')->nullable()->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at')->nullable();
                $table->unique(['team_a_id', 'team_b_id']);
            });
        }

        if (!Schema::hasTable('booking_bookingreminderlog')) {
            Schema::create('booking_bookingreminderlog', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id');
                $table->string('reminder_type', 20);
                $table->timestamp('sent_at')->nullable();
                $table->unique(['booking_id', 'reminder_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_bookingreminderlog');
        Schema::dropIfExists('booking_teamrivalry');
        Schema::dropIfExists('booking_teamchallengeaction');
        Schema::dropIfExists('booking_teamchallenge');
        Schema::dropIfExists('booking_venuereviewphoto');
        Schema::dropIfExists('booking_venuereview');
        Schema::dropIfExists('booking_sportreviewphoto');
        Schema::dropIfExists('booking_sportreview');
        Schema::dropIfExists('booking_discount');
        Schema::dropIfExists('booking_facility');
        Schema::dropIfExists('booking_booking');
        Schema::dropIfExists('booking_permanentbooking');
        Schema::dropIfExists('booking_notification');
        Schema::dropIfExists('booking_chatmediadownload');
        Schema::dropIfExists('booking_teamchannelfollower');
        Schema::dropIfExists('booking_chatreadreceipt');
        Schema::dropIfExists('booking_chatmessage');
        Schema::dropIfExists('booking_chat_admins');
        Schema::dropIfExists('booking_chat_user');
        Schema::dropIfExists('booking_chat');
        Schema::dropIfExists('booking_team');
        Schema::dropIfExists('booking_venue');
        Schema::dropIfExists('booking_sport');
    }
};
