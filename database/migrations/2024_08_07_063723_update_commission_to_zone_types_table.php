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
        if (Schema::hasTable('zone_types')) {
            if (!Schema::hasColumn('zone_types', 'admin_commission_type_from_driver')) {
                Schema::table('zone_types', function (Blueprint $table) {
                    $table->tinyInteger('admin_commission_type_from_driver')->after('payment_type')->nullable();
                });
            }
            if (!Schema::hasColumn('zone_types', 'admin_commission_from_driver')) {
                Schema::table('zone_types', function (Blueprint $table) {
                    $table->double('admin_commission_from_driver', 10,2)->after('admin_commission_type_from_driver')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone_types', function (Blueprint $table) {
            //
        });
    }
};
