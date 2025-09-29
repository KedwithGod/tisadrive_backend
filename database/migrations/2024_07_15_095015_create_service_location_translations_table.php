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
        Schema::create('service_location_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('service_location_id');
            $table->string('name'); 
            $table->string('locale'); 
            $table->timestamps();

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
        Schema::dropIfExists('service_location_translations');
    }
};
