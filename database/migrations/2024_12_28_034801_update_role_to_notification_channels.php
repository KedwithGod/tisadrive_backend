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
            
            if (!Schema::hasColumn('notification_channels', 'role')) {
                Schema::table('notification_channels', function (Blueprint $table) {
                    $table->text('role')->after('id')->nullable();
                });
            }

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
