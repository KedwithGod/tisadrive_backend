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
        if(Schema::hasTable('zone_surge_prices')) {
            if(Schema::hasColumn('zone_surge_prices','zone_id')) {
                Schema::table('zone_surge_prices', function (Blueprint $table) {
                    $table->dropForeign(['zone_id']);
                    $table->dropColumn('zone_id');
                });
            }
            if(!Schema::hasColumn('zone_surge_prices','zone_type_id')) {
                Schema::table('zone_surge_prices', function (Blueprint $table) {
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
        if(Schema::hasColumn('zone_surge_prices','zone_type_id')) {
            Schema::table('zone_surge_prices', function (Blueprint $table) {
                $table->dropForeign(['zone_type_id']);
                $table->dropColumn('zone_type_id');
            });
        }
        if(!Schema::hasColumn('zone_surge_prices','zone_id')) {
            Schema::table('zone_surge_prices', function (Blueprint $table) {
                $table->uuid('zone_id')->nullable()->after('id');
                $table->foreign('zone_id')
                        ->references('id')
                        ->on('zones')
                        ->onDelete('cascade');
            });
        }
    }
};
