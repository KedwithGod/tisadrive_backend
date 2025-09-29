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
        Schema::create('support_ticket_multi_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('ticket_id')->nullable();
            $table->string('image_name');    
            $table->timestamps();
            $table->foreign('ticket_id')
            ->references('id')
            ->on('support_tickets')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_multi_files');
    }
};
