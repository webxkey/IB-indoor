<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for bookings table.
     * Stores all booking records with scheduling and payment information.
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            // Primary key
            $table->id('booking_id');
            
            // Relationships
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')
                  ->comment('User who made booking (nullable for guests)');
            $table->foreignId('complex_id')->constrained('complexes')->onDelete('cascade')
                  ->comment('Reference to complex');
            $table->foreignId('game_id')->constrained('sports', 'game_id')->onDelete('cascade')
                  ->comment('Reference to sport');
            
            // Booking details (denormalized for performance)
            $table->string('game_name')->comment('Cached sport name at time of booking');
            $table->string('user_name')->comment('Name of person booking');
            $table->string('user_number')->comment('Contact number for booking');
            $table->string('court_number')->nullable()->comment('Specific court number if applicable');
            
            // Booking type
            $table->boolean('permanent')->default(false)
                  ->comment('Whether this is a permanent/recurring booking');
            
            // Scheduling
            $table->date('booking_date')->comment('Date of booking');
            $table->time('start_time')->comment('Booking start time');
            $table->time('end_time')->comment('Booking end time');
            $table->integer('duration')->comment('Duration in minutes');
            
            // Financial
            $table->decimal('price', 10, 2)->comment('Total booking price');
            $table->enum('payment_status', ['Pending', 'Paid', 'Failed'])->default('Pending')
                  ->comment('Payment processing status');
            $table->string('payment_method')->nullable()->comment('Stripe/Cash/Card/Wallet');
            
            // Status tracking
            $table->enum('status', ['Upcoming', 'Completed', 'Cancelled', 'No-Show'])->default('Upcoming')
                  ->comment('Booking lifecycle status');
            
            // Additional info
            $table->text('notes')->nullable()->comment('User notes/special requests');
            $table->string('qr_code')->nullable()->comment('QR code identifier');
            $table->text('admin_comments')->nullable()->comment('Admin notes about booking');
            
            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};