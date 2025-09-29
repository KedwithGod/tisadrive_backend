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
                if (!Schema::hasColumn('notification_channels', 'push_title')) {
                    $table->text('push_title')->after('show_linkedinicon');
                }
                if (!Schema::hasColumn('notification_channels', 'push_body')) {
                    $table->text('push_body')->after('push_title');
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
