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
    {if (Schema::hasTable('landing_headers')) {
            
        if (!Schema::hasColumn('landing_headers', 'aboutus')) {
            Schema::table('landing_headers', function (Blueprint $table) {
                $table->text('aboutus')->after('home')->nullable();
            });
        }

    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_headers', function (Blueprint $table) {
            //
        });
    }
};
