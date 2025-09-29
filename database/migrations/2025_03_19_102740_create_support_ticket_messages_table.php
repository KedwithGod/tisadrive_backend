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
        if (!Schema::hasTable('support_ticket_messages')) {
        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ticket_id');
            $table->unsignedInteger('user_id');
            $table->longText('employee_id')->nullable();
            $table->longText('message')->nullable(); // Message content
            $table->boolean('is_read')->default(false); // Read status
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('users_id')->on('support_tickets')->onDelete('cascade'); // User who sends the message
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_messages');
    }
};
