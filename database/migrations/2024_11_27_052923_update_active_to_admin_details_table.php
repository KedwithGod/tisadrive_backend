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
        if (Schema::hasTable('admin_details')) {
            // Add image_type column if it doesn't already exist
            Schema::table('admin_details', function (Blueprint $table) {
                if (!Schema::hasColumn('admin_details', 'active')) {
                    $table->boolean('active')->after('city')->default(0);
                }
            });
        
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admin_details')) {
            if (Schema::hasColumn('admin_details', 'active')) {
                Schema::table('admin_details', function (Blueprint $table) {
                  $table->dropColumn('active');
                });
            }
        }
    }
};
