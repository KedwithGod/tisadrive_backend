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
                if (!Schema::hasColumn('requests', 'paid_at')) {
                    $table->string('paid_at')->nullable()->after('is_parcel');
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
