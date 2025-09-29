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
            // Add image_type column if it doesn't already exist
            Schema::table('requests', function (Blueprint $table) {
                if (!Schema::hasColumn('requests', 'is_parcel')) {
                    $table->boolean('is_parcel')->default(0)->after('is_airport');
                }
            });
        
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
