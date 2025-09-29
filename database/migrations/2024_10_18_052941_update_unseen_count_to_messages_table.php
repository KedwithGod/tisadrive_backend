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
        if (Schema::hasTable('messages')) {
            // Add image_type column if it doesn't already exist
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'unseen_count')) {
                    $table->boolean('unseen_count')->after('sender_id')->default(false);
                }
            });
        
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            //
        });
    }
};
