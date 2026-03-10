<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('users_user')) {
            Schema::create('users_user', function (Blueprint $table) {
                $table->id();
                $table->string('password');
                $table->timestamp('last_login')->nullable();
                $table->boolean('is_superuser')->default(false);
                $table->string('email')->nullable()->unique();
                $table->string('phone_number', 30)->nullable()->unique();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_staff')->default(false);
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('profile_picture')->nullable();
                $table->string('address')->nullable();
                $table->text('bio')->nullable();
                $table->json('sports_preferences')->nullable();
                $table->string('availability', 20)->nullable();
                $table->boolean('is_public_profile')->default(true);
                $table->boolean('is_show_contact')->default(false);
                $table->unsignedInteger('points')->default(0);
                $table->string('referral_code')->nullable()->unique();
            });
        }

        if (!Schema::hasTable('users_pointtransaction')) {
            Schema::create('users_pointtransaction', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('amount')->default(0);
                $table->string('reason')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('users_device')) {
            Schema::create('users_device', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('token')->unique();
                $table->string('app_version')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_active')->nullable();
                $table->string('onesignal_id')->nullable()->unique();
                $table->string('platform', 20)->default('unknown');
                $table->timestamp('created_at')->nullable();
                $table->index(['user_id', 'is_active']);
                $table->index(['platform', 'is_active']);
            });
        }

        if (!Schema::hasTable('users_favorite')) {
            Schema::create('users_favorite', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('venue_id')->nullable();
                $table->unsignedBigInteger('sport_id')->nullable();
                $table->string('type', 20);
                $table->timestamp('added_at')->nullable();
                $table->unique(['user_id', 'type', 'venue_id', 'sport_id']);
            });
        }

        if (!Schema::hasTable('users_referral')) {
            Schema::create('users_referral', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('referrer_id');
                $table->unsignedBigInteger('referred_user_id');
                $table->timestamp('created_at')->nullable();
                $table->unique(['referrer_id', 'referred_user_id']);
            });
        }

        if (!Schema::hasTable('user_otps')) {
            Schema::create('user_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->string('otp_code', 10);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_used')->default(false);
                $table->index(['email', 'otp_code']);
            });
        }

        if (!Schema::hasTable('password_reset_otps')) {
            Schema::create('password_reset_otps', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->string('otp_code', 10);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_used')->default(false);
                $table->index(['email', 'otp_code']);
            });
        }

        if (!Schema::hasTable('users_deletionfeedback')) {
            Schema::create('users_deletionfeedback', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('reason', 40);
                $table->text('feedback')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('users_bugreport')) {
            Schema::create('users_bugreport', function (Blueprint $table) {
                $table->id();
                $table->string('full_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('subject')->nullable();
                $table->text('comment')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users_bugreport');
        Schema::dropIfExists('users_deletionfeedback');
        Schema::dropIfExists('password_reset_otps');
        Schema::dropIfExists('user_otps');
        Schema::dropIfExists('users_referral');
        Schema::dropIfExists('users_favorite');
        Schema::dropIfExists('users_device');
        Schema::dropIfExists('users_pointtransaction');
        Schema::dropIfExists('users_user');
    }
};
