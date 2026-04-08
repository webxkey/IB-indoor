<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('booking_waitlist')) {
            Schema::create('booking_waitlist', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sport_id');
                $table->string('booking_date');
                $table->string('time_slot');
                $table->string('court_number')->nullable();
                $table->string('customer_name');
                $table->string('customer_phone', 30);
                $table->string('status')->default('waiting'); // waiting, notified, booked
                $table->timestamp('notified_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_waitlist');
    }
};
