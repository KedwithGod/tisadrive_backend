<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('zone_types')) {
            
            if (Schema::hasColumn('zone_types', 'admin_commission_type')) {
                Schema::table('zone_types', function (Blueprint $table) {
                  $table->dropColumn('admin_commission_type');
                });
            }

            if (Schema::hasColumn('zone_types', 'admin_commission')) {
                Schema::table('zone_types', function (Blueprint $table) {
                    $table->dropColumn('admin_commission');
                });
            }
            if (!Schema::hasColumn('zone_types', 'admin_commission_type_for_owner')) {
                Schema::table('zone_types', function (Blueprint $table) {
                  $table->tinyInteger('admin_commission_type_for_owner')->after('payment_type')->nullable();
                });
            }

            if (!Schema::hasColumn('zone_types', 'admin_commission_for_owner')) {
                Schema::table('zone_types', function (Blueprint $table) {
                    $table->double('admin_commission_for_owner',10,2)->after('admin_commission_type_for_owner')->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('zone_types')) {
            if (Schema::hasColumn('zone_types', 'admin_commission_type_for_owner')) {
                Schema::table('zone_types', function (Blueprint $table) {
                  $table->dropColumn('admin_commission_type_for_owner');
                });
            }

            if (Schema::hasColumn('zone_types', 'admin_commission_for_owner')) {
                Schema::table('zone_types', function (Blueprint $table) {
                    $table->dropColumn('admin_commission_for_owner');
                });
            }
        }
    }
};
