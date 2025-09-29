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
        if(Schema::hasTable('zones')){
            if(!Schema::hasColumn('zones','maximum_distance')){
                Schema::table('zones', function (Blueprint $table) {
                    $table->double('maximum_distance',10,2)->default(0)->after('unit');
                });
            }
            if(!Schema::hasColumn('zones','maximum_outstation_distance')){
                Schema::table('zones', function (Blueprint $table) {
                    $table->double('maximum_outstation_distance',10,2)->default(0)->after('maximum_distance');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('zones')){
            if(Schema::hasColumn('zones','maximum_distance')){
                Schema::table('zones', function (Blueprint $table) {
                    $table->dropColumn('maximum_distance');
                });
            }
            if(Schema::hasColumn('zones','maximum_outstation_distance')){
                Schema::table('zones', function (Blueprint $table) {
                    $table->dropColumn('maximum_outstation_distance');
                });
            }
        }
    }
};
