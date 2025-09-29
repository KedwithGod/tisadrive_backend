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
        Schema::table('support_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('support_tickets', 'service_location_id')) {
                $table->uuid('service_location_id')->after('support_type')->nullable();                
            }
            if (!Schema::hasColumn('support_tickets', 'driver_id')) {
                $table->unsignedInteger('driver_id')->after('users_id')->nullable();                
            }
            $table->unsignedInteger('users_id')->nullable()->change();

            $table->foreign('service_location_id')
            ->references('id')
            ->on('service_locations')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            if(Schema::hasColumn('support_tickets','service_location_id')) {
                Schema::table('support_tickets', function (Blueprint $table) {
                    $table->dropColumn('service_location_id');
                });
            }
            if(Schema::hasColumn('support_tickets','driver_id')) {
                Schema::table('support_tickets', function (Blueprint $table) {
                    $table->dropColumn('driver_id');
                });
            }

        });
    }
};
