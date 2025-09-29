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
        if(Schema::hasTable('requests')) {
            if(!Schema::hasColumn('requests','is_trip_meter')) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->boolean('is_trip_meter')->default(false)->after('is_bid_ride');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('requests')) {
            if(!Schema::hasColumn('requests','is_trip_meter')) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->dropColumn('is_trip_meter');
                });
            }
        }
    }
};
