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
        if (!Schema::hasTable('group_members')) {
            Schema::create('group_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->unsignedBigInteger('traveller_id');  // Changed from user_id to traveller_id
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamps();

                $table->unique(['group_id', 'traveller_id']);
                
                // Add foreign key constraints if tables exist
                if (Schema::hasTable('groups')) {
                    $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
                }
                
                if (Schema::hasTable('travellers')) {
                    $table->foreign('traveller_id')->references('id')->on('travellers')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
