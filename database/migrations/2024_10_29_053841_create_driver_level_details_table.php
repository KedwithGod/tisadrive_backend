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
        Schema::create('driver_level_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('driver_id');
            $table->uuid('level_id');
            $table->unsignedInteger('level');
            $table->boolean('amount_rewarded')->default(0);
            $table->boolean('ride_rewarded')->default(0);
            $table->timestamps();

            $table->foreign('driver_id')
                    ->references('id')
                    ->on('drivers')
                    ->onDelete('cascade');

            $table->foreign('level_id')
                    ->references('id')
                    ->on('driver_level_ups')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_level_details');
    }
};
