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
        if (Schema::hasTable('fleet_documents')) {
            // Add image column if it doesn't already exist
            Schema::table('fleet_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('fleet_documents', 'back_image')) {
                    $table->string('back_image')->after('image')->nullable();
                }
            });
    
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_documents', function (Blueprint $table) {
            //
        });
    }
};
