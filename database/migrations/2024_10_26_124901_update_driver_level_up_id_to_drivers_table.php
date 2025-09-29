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
        if (Schema::hasTable('drivers')) {
            // Add image_type column if it doesn't already exist
            Schema::table('drivers', function (Blueprint $table) {
                if (!Schema::hasColumn('drivers', 'driver_level_up_id')) {
                    $table->uuid('driver_level_up_id')->nullable();
                }
            });
        
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            //
        });
    }
};
