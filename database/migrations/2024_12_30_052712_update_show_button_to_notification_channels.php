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
        if (Schema::hasTable('notification_channels')) {
            
            Schema::table('notification_channels', function (Blueprint $table) {
                if (!Schema::hasColumn('notification_channels', 'show_button')) {
                    $table->boolean('show_button')->after('button_url');
                }
                if (!Schema::hasColumn('notification_channels', 'show_img')) {
                    $table->boolean('show_img')->after('banner_img');
                }
                if (!Schema::hasColumn('notification_channels', 'show_fbicon')) {
                    $table->boolean('show_fbicon')->after('footer_copyrights');
                }
                if (!Schema::hasColumn('notification_channels', 'show_instaicon')) {
                    $table->boolean('show_instaicon')->after('show_fbicon');
                }
                if (!Schema::hasColumn('notification_channels', 'show_twittericon')) {
                    $table->boolean('show_twittericon')->after('show_instaicon');
                }
                if (!Schema::hasColumn('notification_channels', 'show_linkedinicon')) {
                    $table->boolean('show_linkedinicon')->after('show_twittericon');
                }
            });

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_channels', function (Blueprint $table) {
            //
        });
    }
};
