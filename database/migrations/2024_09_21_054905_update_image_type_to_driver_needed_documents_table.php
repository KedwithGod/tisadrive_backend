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
        if (Schema::hasTable('driver_needed_documents')) {
            // Add image_type column if it doesn't already exist
            Schema::table('driver_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('driver_needed_documents', 'image_type')) {
                    $table->string('image_type')->after('account_type')->nullable();
                }
            });
        
            // Add is_editable column if it doesn't already exist
            Schema::table('driver_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('driver_needed_documents', 'is_editable')) {
                    $table->boolean('is_editable')->after('image_type')->default(true);
                }
            });
        }
             
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_needed_documents', function (Blueprint $table) {
            //
        });
    }
};
