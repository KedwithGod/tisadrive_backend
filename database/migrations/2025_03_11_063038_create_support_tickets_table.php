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
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('ticket_id');
                $table->uuid('title_id');
                $table->unsignedInteger('users_id');
                $table->longText('description')->nullable();
                $table->longText('assign_to')->nullable();
                $table->string('request_id')->nullable();
                $table->tinyInteger('status')->comment('1 => open,2 => acknowledge,3 => closed');
                $table->string('support_type');
                $table->timestamps();

                $table->foreign('title_id')
                ->references('id')
                ->on('support_ticket_titles')
                ->onDelete('cascade');

                $table->foreign('users_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
