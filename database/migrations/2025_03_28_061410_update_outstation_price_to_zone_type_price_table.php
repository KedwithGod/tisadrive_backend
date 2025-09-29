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
                if (!Schema::hasColumn('zone_type_price', 'outstation_base_price')) {
                    $table->double('outstation_base_price', 10, 2)->after('cancellation_fee')->default(0);
                }
            });
            Schema::table('zone_type_price', function (Blueprint $table) {
                if (!Schema::hasColumn('zone_type_price', 'outstation_price_per_distance')) {
                    $table->double('outstation_price_per_distance', 10, 2)->after('outstation_base_price')->default(0);
                }
            });
            Schema::table('zone_type_price', function (Blueprint $table) {
                if (!Schema::hasColumn('zone_type_price', 'outstation_base_distance')) {
                    $table->integer('outstation_base_distance')->after('outstation_price_per_distance');
                }
            });
            Schema::table('zone_type_price', function (Blueprint $table) {
                if (!Schema::hasColumn('zone_type_price', 'outstation_price_per_time')) {
                    $table->double('outstation_price_per_time', 10, 2)->after('outstation_base_distance')->default(0);
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
