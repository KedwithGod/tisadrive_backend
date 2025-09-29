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
            
            if (!Schema::hasColumn('zone_types', 'airport_surge')) {
                Schema::table('zone_types', function (Blueprint $table) {
                  $table->double('airport_surge', 10, 2)->after('service_tax')->nullable();
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
