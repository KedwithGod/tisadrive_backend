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
        if (Schema::hasTable('driver_incentive_histories')) {
            // Add image_type column if it doesn't already exist
            if (!Schema::hasColumn('driver_incentive_histories', 'ride_count')) {
                Schema::table('driver_incentive_histories', function (Blueprint $table) {
                    $table->integer('ride_count')->after('driver_id')->nullable();
                });
            }
        
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('driver_incentive_histories')) {
            // Add image_type column if it doesn't already exist
            if (Schema::hasColumn('driver_incentive_histories', 'ride_count')) {
                Schema::table('driver_incentive_histories', function (Blueprint $table) {
                    $table->dropColumn('ride_count');
                });
            }
        
        }
    }
};
