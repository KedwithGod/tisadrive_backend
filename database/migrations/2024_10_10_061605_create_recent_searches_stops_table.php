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
        Schema::create('recent_searches_stops', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('recent_searche_id')->nullable();
            $table->string('address')->nullable();
            $table->string('short_address')->nullable();
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude', 15, 8)->nullable();
            $table->string('poc_name')->nullable();
            $table->string('poc_mobile', 14)->nullable();
            $table->string('poc_instruction', 14)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();


            $table->foreign('recent_searche_id')
                    ->references('id')
                    ->on('recent_searches')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recent_searches_stops');
    }
};
