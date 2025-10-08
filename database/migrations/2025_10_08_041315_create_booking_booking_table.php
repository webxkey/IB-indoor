<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *///
   public function up(): void
{
    Schema::create('booking_booking', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('game_name', 255);
        $table->string('user_name', 255);
        $table->string('user_number', 255);
        $table->string('court_number', 255)->nullable();
        $table->boolean('permanent');
        $table->date('booking_date');
        $table->time('start_time', 6);
        $table->time('end_time', 6);
        $table->integer('duration');
        $table->decimal('price', 10, 2);
        $table->string('payment_status', 20);
        $table->string('payment_method', 255)->nullable();
        $table->string('status', 20);
        $table->longText('notes')->nullable();
        $table->string('qr_code', 255)->nullable();
        $table->longText('admin_comments')->nullable();
        $table->dateTime('created_at', 6);
        $table->dateTime('updated_at', 6);
        $table->date('date')->nullable();
        $table->string('time_slot', 200)->nullable();
        $table->decimal('total', 10, 2)->nullable();
        $table->unsignedBigInteger('user_id_id')->nullable();
        $table->unsignedBigInteger('game_id_id');
        $table->unsignedBigInteger('complex_id_id');
        $table->unsignedBigInteger('sport_id')->nullable();

        // If you want to add foreign key constraints, you can uncomment and modify these:
        // $table->foreign('user_id_id')->references('id')->on('users')->onDelete('set null');
        // $table->foreign('game_id_id')->references('id')->on('games')->onDelete('cascade');
        // $table->foreign('complex_id_id')->references('id')->on('complexes')->onDelete('cascade');
        // $table->foreign('sport_id')->references('id')->on('sports')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_booking');
    }
};
