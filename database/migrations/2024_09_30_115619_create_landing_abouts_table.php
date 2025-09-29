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
        Schema::create('landing_abouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('hero_title')->nullable();
            $table->text('about_heading')->nullable();
            $table->longText('about_title')->nullable();
            $table->longText('about_para' ,255)->nullable();
            $table->text('about_lists')->nullable();
            $table->longText('about_img')->nullable();
            $table->text('ceo_name')->nullable();
            $table->text('ceo_title')->nullable();
            $table->text('signature')->nullable();
            $table->longText('ceo_para' ,255)->nullable();
            $table->text('ceo_img')->nullable();
            $table->text('vision_mision_heading')->nullable();
            $table->longText('vision_title')->nullable();
            $table->longText('vision_para' ,255)->nullable(); 
            $table->text('mission_title')->nullable(); 
            $table->longText('mission_para' ,255)->nullable();
            $table->text('team_title')->nullable();
            $table->longText('team_para' ,255)->nullable(); 
            $table->text('team_members')->nullable();
            $table->text('testimonial_heading')->nullable(); 
            $table->text('testimonial_content')->nullable();
            $table->text('locale')->nullable();
            $table->text('language')->nullable();
            $table->text('direction')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_abouts');
    }
};
