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
        Schema::create('driver_level_ups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('level');
            $table->string('name');
            $table->string('reward_type')->nullable();
            $table->double('reward', 10,2)->default(0);
            $table->boolean('is_min_ride_complete')->default(0);
            $table->integer('min_ride_count')->nullable();
            $table->double('ride_points', 10,2)->default(0);
            $table->boolean('is_min_ride_amount_complete')->default(0);
            $table->integer('min_ride_amount')->nullable();
            $table->double('amount_points', 10,2)->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_level_ups');
    }
};
