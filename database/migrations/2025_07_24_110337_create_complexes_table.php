<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('complexes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('Indoor');
            $table->text('address');
            $table->string('county');
            $table->string('location');
            $table->string('postal_code');
            $table->string('contact_number');
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('status')->default('Active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complexes');
    }
};