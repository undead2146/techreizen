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
        // Check if the table already exists
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->unsignedBigInteger('trip_id'); // Changed from foreignId to avoid immediate constraints
                $table->unsignedBigInteger('created_by'); // Changed from foreignId
                $table->integer('max_members')->default(10);
                $table->timestamps();
                
                // Add foreign key constraints after table creation
                if (Schema::hasTable('trips')) {
                    $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
                }
                
                if (Schema::hasTable('users')) {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
