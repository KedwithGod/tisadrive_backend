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
        Schema::create('onboarding_screen_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('onboarding_screen_id');
            $table->string('title'); 
            $table->longText('description');
            $table->string('locale'); 
            $table->timestamps();

            $table->foreign('onboarding_screen_id')
            ->references('id')
            ->on('onboarding_screen')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboard_screens_translations');
    }
};
