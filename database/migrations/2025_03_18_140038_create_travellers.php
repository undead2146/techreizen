<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travellers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Changed to unsignedBigInteger
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('trip_id'); // New field for trip_id
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade'); // Foreign key constraint
            $table->unsignedBigInteger('zip_id')->length(10);
            $table->foreign('zip_id')->references('id')->on('cities')->onDelete('cascade');
            $table->integer('major_id')->length(10);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('country');
            $table->string('address');
            $table->string('gender');
            $table->string('phone');
            $table->string('emergency_phone_1');
            $table->string('emergency_phone_2');
            $table->string('nationality');
            $table->date('birthdate');
            $table->string('birthplace');
            $table->string('iban');
            $table->string('bic');
            $table->tinyInteger('medical_issue');
            $table->string('medical_info');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travellers');
    }
};
