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
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('topics')->nullable();
            $table->text('topics_content')->nullable();
            $table->boolean('push_notification')->nullable();
            $table->boolean('mail')->nullable();
            $table->boolean('sms')->nullable();
            $table->text('email_subject')->nullable();
            $table->longText('logo_img')->nullable();
            $table->longText('mail_body')->nullable();
            $table->text('button_name')->nullable();
            $table->text('button_url')->nullable();
            $table->longText('banner_img')->nullable();
            $table->text('footer')->nullable();
            $table->text('footer_content')->nullable();
            $table->text('footer_copyrights')->nullable();
            $table->text('translation_dataset')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_channels');
    }
};
