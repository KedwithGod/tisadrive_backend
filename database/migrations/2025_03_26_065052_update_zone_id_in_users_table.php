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
        if(Schema::hasTable('users')){
            if(!Schema::hasColumn('users','zone_id')){
                Schema::table('users', function (Blueprint $table) {
                    $table->uuid('zone_id')->nullable()->after('lang');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('users')){
            if(!Schema::hasColumn('users','zone_id')){
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('zone_id');
                });
            }
        }
    }
};
