<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOwnersNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('owners', function (Blueprint $table) {
            // Make foreign key columns nullable
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->uuid('service_location_id')->nullable()->change();
            
            // Make string columns nullable
            $table->string('company_name')->nullable()->change();
            $table->string('owner_name')->nullable()->change();
            $table->string('tax_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('owners', function (Blueprint $table) {
        });
    }
}
