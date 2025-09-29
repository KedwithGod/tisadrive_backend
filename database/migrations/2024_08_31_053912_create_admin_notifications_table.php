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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('url');
            $table->string('notification_type');
            $table->boolean('is_read');
            $table->unsignedInteger('user_id');
            $table->timestamps();
//foregin key
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
                    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
