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
        Schema::create('landing_headers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('header_logo' ,255)->nullable();
            $table->text('home')->nullable();
            $table->text('driver')->nullable();
            $table->text('user')->nullable();
            $table->text('contact')->nullable();
            $table->text('book_now_btn')->nullable();
            $table->text('footer_logo' ,255)->nullable();
            $table->longText('footer_para')->nullable();
            $table->text('quick_links')->nullable();
            $table->text('compliance')->nullable();
            $table->text('privacy')->nullable();
            $table->text('terms')->nullable();
            $table->text('dmv')->nullable();
            $table->text('user_app')->nullable();
            $table->text('user_play')->nullable();
            $table->text('user_play_link')->nullable();
            $table->text('user_apple')->nullable();
            $table->text('user_apple_link')->nullable();
            $table->text('driver_app')->nullable();
            $table->text('driver_play')->nullable();
            $table->text('driver_play_link')->nullable();
            $table->text('driver_apple')->nullable();
            $table->text('driver_apple_link')->nullable();
            $table->text('copy_rights')->nullable();
            $table->text('fb_link')->nullable();
            $table->text('linkdin_link')->nullable();
            $table->text('x_link')->nullable();
            $table->text('insta_link')->nullable();
            $table->text('locale')->nullable();
            $table->text('language')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_headers');
    }
};
