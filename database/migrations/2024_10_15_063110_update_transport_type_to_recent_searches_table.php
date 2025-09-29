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
        if (Schema::hasTable('recent_searches')) {
            // Add image_type column if it doesn't already exist
            Schema::table('recent_searches', function (Blueprint $table) {
                if (!Schema::hasColumn('recent_searches', 'transport_type')) {
                    $table->string('transport_type')->nullable()->after('user_id');
                }
            });
        
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recent_searches', function (Blueprint $table) {
            //
        });
    }
};
