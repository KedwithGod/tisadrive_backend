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
        if (Schema::hasTable('fleets')) {            
            if (!Schema::hasColumn('fleets', 'status')) {
                Schema::table('fleets', function (Blueprint $table) {
                    $table->string('status')->after('lang')->after('approve')->default('2');
                    });
            }   

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleets', function (Blueprint $table) {
            //
        });
    }
};
