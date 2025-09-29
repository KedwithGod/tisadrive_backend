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
        Schema::table('zone_type_price', function (Blueprint $table) {
            Schema::table('zone_type_price', function (Blueprint $table) {
                if (!Schema::hasColumn('zone_type_price', 'cancellation_fee_for_user')) {
                    $table->integer('cancellation_fee_for_user')->after('outstation_price_per_time')->default(60)->comment('In percentage');
                }
            });
            Schema::table('zone_type_price', function (Blueprint $table) {
                if (!Schema::hasColumn('zone_type_price', 'cancellation_fee_for_driver')) {
                    $table->integer('cancellation_fee_for_driver')->after('cancellation_fee_for_user')->default(60)->comment('In percentage');
                }
            });
            Schema::table('zone_type_price', function (Blueprint $table) {
                if (!Schema::hasColumn('zone_type_price', 'fee_goes_to')) {
                    $table->enum('fee_goes_to',['driver','admin','partially_driver_admin'])->after('cancellation_fee_for_driver')->default('admin');
                }
            });
            Schema::table('zone_type_price', function (Blueprint $table) {
                if (!Schema::hasColumn('zone_type_price', 'driver_get_fee_percentage')) {
                    $table->integer('driver_get_fee_percentage')->after('fee_goes_to')->default(0)->comment('In percentage');
                }
            });
            Schema::table('zone_type_price', function (Blueprint $table) {
                if (!Schema::hasColumn('zone_type_price', 'admin_get_fee_percentage')) {
                    $table->integer('admin_get_fee_percentage')->after('driver_get_fee_percentage')->default(0)->comment('In percentage');
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone_type_price', function (Blueprint $table) {
            //
        });
    }
};
