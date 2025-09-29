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
        if (Schema::hasTable('requests')) {            
            if (!Schema::hasColumn('requests', 'is_airport')) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->boolean('is_airport')->after('lang')->default(0)->after('is_completed');
                    });
            }   

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            //
        });
    }
};
