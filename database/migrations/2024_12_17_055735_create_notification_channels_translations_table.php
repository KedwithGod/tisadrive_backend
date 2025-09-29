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
        Schema::create('notification_channels_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('notification_channel_id');
            $table->string('email_subject')->nullable();
            $table->text('mail_body')->nullable();
            $table->string('button_name')->nullable();
            $table->text('footer_content')->nullable();
            $table->text('footer_copyrights')->nullable();
            $table->string('locale'); 
            $table->timestamps();

            $table->foreign('notification_channel_id', 'fk_notification_channel_id')
                    ->references('id')
                    ->on('notification_channels')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_channels_translations');
    }
};
