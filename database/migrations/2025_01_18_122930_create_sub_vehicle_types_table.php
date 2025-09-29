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
        Schema::create('sub_vehicle_types', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('vehicle_type_id');
            $table->uuid('sub_vehicle_type_id');
            $table->timestamps();

            $table->foreign('vehicle_type_id')
            ->references('id')
            ->on('vehicle_types')
            ->onDelete('cascade');

            $table->foreign('sub_vehicle_type_id')
            ->references('id')
            ->on('vehicle_types')
            ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_vehicle_types');
    }
};
