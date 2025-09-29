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
        Schema::table('drivers', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['service_location_id']);

            // Make the column nullable
            $table->uuid('service_location_id')->nullable()->change();

            // Re-add the foreign key constraint
            $table->foreign('service_location_id')->references('id')->on('service_locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['service_location_id']);

            // Revert the column to be non-nullable
            $table->uuid('service_location_id')->nullable(false)->change();

            // Re-add the foreign key constraint
            $table->foreign('service_location_id')->references('id')->on('service_locations');
        });
    }
};
