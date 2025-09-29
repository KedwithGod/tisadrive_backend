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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Setting UUID as primary key
            $table->uuid('conversation_id'); // UUID reference to the conversations table
            $table->string('sender_id'); // User or admin ID
            $table->string('sender_type'); // To identify if it's a user or admin
            $table->text('content'); // Message content
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('conversation_id')
                  ->references('id')
                  ->on('conversations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
