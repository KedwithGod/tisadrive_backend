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
        Schema::table('zone_types', function (Blueprint $table) {
            if (!Schema::hasColumn('zone_types', 'support_airport_fee')) {
                $table->boolean('support_airport_fee')->after('airport_surge')->default(false);
            }
        });
        Schema::table('zone_types', function (Blueprint $table) {
            if (!Schema::hasColumn('zone_types', 'support_outstation')) {
                $table->boolean('support_outstation')->after('support_airport_fee')->default(false);
            }
        });
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
