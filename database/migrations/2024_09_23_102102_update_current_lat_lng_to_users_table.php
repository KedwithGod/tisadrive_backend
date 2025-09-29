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
        if (Schema::hasTable('users')) {            
            if (!Schema::hasColumn('users', 'current_lat')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->double('current_lat', 15, 8)->after('lang')->nullable();
                    });
            }   
            if (!Schema::hasColumn('users', 'current_lng')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->double('current_lng', 15, 8)->after('current_lat')->nullable();     
                    });
            }  

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
