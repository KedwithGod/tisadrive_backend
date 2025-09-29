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

        if (Schema::hasTable('landing_drivers')) {
            
            if (!Schema::hasColumn('landing_drivers', 'direction')) {
                Schema::table('landing_drivers', function (Blueprint $table) {
                    $table->text('direction')->after('language')->nullable();
                });
            }

        }        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_drivers', function (Blueprint $table) {
            //
        });
    }
};
