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
        if (Schema::hasTable('owner_needed_documents')) {
            // Add image_type column if it doesn't already exist
            Schema::table('owner_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('owner_needed_documents', 'image_type')) {
                    $table->string('image_type')->after('name')->nullable();
                }
            });
        
            // Add is_editable column if it doesn't already exist
            Schema::table('owner_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('owner_needed_documents', 'is_editable')) {
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
        Schema::table('owner_needed_documents', function (Blueprint $table) {
            //
        });
    }
};
