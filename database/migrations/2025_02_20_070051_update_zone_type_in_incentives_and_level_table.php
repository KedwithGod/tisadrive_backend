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
        if(Schema::hasTable('incentives')) {
            if(!Schema::hasColumn('incentives','zone_type_id')) {
                Schema::table('incentives', function (Blueprint $table) {
                    $table->uuid('zone_type_id')->nullable()->after('id');
                    
                    $table->foreign('zone_type_id')
                    ->references('id')
                    ->on('zone_types')
                    ->onDelete('cascade');
                });
            }
        }
        if(Schema::hasTable('driver_level_ups')) {
            if(!Schema::hasColumn('driver_level_ups','zone_type_id')) {
                Schema::table('driver_level_ups', function (Blueprint $table) {
                    $table->uuid('zone_type_id')->nullable()->after('id');

                    $table->foreign('zone_type_id')
                    ->references('id')
                    ->on('zone_types')
                    ->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('incentives')) {
            if(Schema::hasColumn('incentives','zone_type_id')) {
                Schema::table('incentives', function (Blueprint $table) {
                    $table->dropColumn('zone_type_id');
                });
            }
        }
        if(Schema::hasTable('driver_level_ups')) {
            if(Schema::hasColumn('driver_level_ups','zone_type_id')) {
                Schema::table('driver_level_ups', function (Blueprint $table) {
                    $table->dropColumn('zone_type_id');
                });
            }
        }
    }
};
