<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('password', 128);
            $table->dateTime('last_login', 6)->nullable();
            $table->boolean('is_superuser')->default(false);
            $table->string('email', 254)->unique()->nullable();
            $table->string('phone_number', 15)->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_staff')->default(false);
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('profile_picture', 100)->nullable();
            $table->longText('address')->nullable();
            $table->longText('bio')->nullable();
            $table->json('sports_preferences')->nullable();
            $table->json('teams')->nullable();
            $table->string('availability', 10);
            $table->boolean('is_public_profile')->default(false);
            $table->boolean('is_show_contact')->default(false);
            $table->unsignedInteger('points')->default(0);
            $table->string('referral_code', 20)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_user');
    }
};
