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
        if (Schema::hasTable('driver_vehicle_types')) {
            if (!Schema::hasColumn('driver_vehicle_types', 'signed_vehicle')) {
                Schema::table('driver_vehicle_types', function (Blueprint $table) {
                    $table->boolean('signed_vehicle')->after('vehicle_type')->default(true);
                });
            }             
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('driver_vehicle_types')) {
            if (Schema::hasColumn('driver_vehicle_types', 'signed_vehicle')) {
                Schema::table('driver_vehicle_types', function (Blueprint $table) {
                    $table->dropColumn('signed_vehicle');
                });
            }
        }
    }
};
