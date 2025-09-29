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
        Schema::create('landing_homes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('hero_title')->nullable();
            $table->text('hero_user_link_android')->nullable();
            $table->text('hero_user_link_apple')->nullable();
            $table->text('hero_driver_link_android')->nullable();
            $table->text('hero_driver_link_apple')->nullable();
            $table->text('feature_heading')->nullable();
            $table->longText('feature_para')->nullable();
            $table->text('feature_sub_heading_1')->nullable(); 
            $table->longText('feature_sub_para_1')->nullable();
            $table->text('feature_sub_heading_2')->nullable(); 
            $table->longText('feature_sub_para_2')->nullable();
            $table->text('feature_sub_heading_3')->nullable(); 
            $table->longText('feature_sub_para_3')->nullable();
            $table->text('feature_sub_heading_4')->nullable(); 
            $table->longText('feature_sub_para_4')->nullable();
            $table->text('service_heading_1')->nullable();
            $table->text('service_heading_2')->nullable();
            $table->longText('service_para')->nullable();
            $table->text('services')->nullable();
            $table->text('service_img' ,255)->nullable();
            $table->text('about_title_1')->nullable();
            $table->text('about_title_2')->nullable();
            $table->text('about_img' ,255)->nullable();
            $table->longText('about_para')->nullable();
            $table->text('about_lists')->nullable();
            $table->text('box_img_1' ,255)->nullable();
            $table->longText('box_para_1')->nullable();
            $table->text('box_img_2' ,255)->nullable();
            $table->longText('box_para_2')->nullable();
            $table->text('box_img_3' ,255)->nullable();
            $table->longText('box_para_3')->nullable();
            $table->text('drive_heading')->nullable();
            $table->text('drive_title_1')->nullable();
            $table->longText('drive_para_1')->nullable();
            $table->text('drive_title_2')->nullable();
            $table->longText('drive_para_2')->nullable();
            $table->text('drive_title_3')->nullable();
            $table->longText('drive_para_3')->nullable();
            $table->text('service_area_img' ,255)->nullable();
            $table->text('service_area_title')->nullable();
            $table->longText('service_area_para')->nullable();
            $table->text('locale')->nullable();
            $table->text('language')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_homes');
    }
};
