<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('venue_staff')) {
            Schema::create('venue_staff', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('venue_id');
                $table->string('name');
                $table->string('role')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('shift')->nullable(); // morning, evening, night
                $table->string('status')->default('active'); // active, inactive
                $table->string('email')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('venue_cameras')) {
            Schema::create('venue_cameras', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('venue_id');
                $table->string('name');
                $table->string('location')->nullable();
                $table->string('stream_url')->nullable();
                $table->string('status')->default('online'); // online, offline, maintenance
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_cameras');
        Schema::dropIfExists('venue_staff');
    }
};
