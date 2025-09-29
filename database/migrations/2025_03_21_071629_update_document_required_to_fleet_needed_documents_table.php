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
        Schema::table('fleet_needed_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('fleet_needed_documents', 'is_required')) {
                $table->boolean('is_required')->after('has_expiry_date')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_needed_documents', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
    }
};
