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
        Schema::create('driver_incentive_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('driver_id');
            $table->double('amount', 10, 2)->default(0);
            $table->enum('mode', ['daily', 'weekly'])->default('daily');
            $table->timestamp('date');
            $table->timestamps();


            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
                ->onDelete('cascade');
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_incentive_histories');
    }
};
