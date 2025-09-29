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
        Schema::create('landing_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('hero_title')->nullable();
            $table->text('user_heading_1')->nullable();
            $table->text('user_para')->nullable();
            $table->text('user_img_1' ,255)->nullable();
            $table->text('user_title_1')->nullable();
            $table->longText('user_para_1' ,10000)->nullable();
            $table->text('user_img_2' ,255)->nullable();
            $table->text('user_title_2')->nullable();
            $table->longText('user_para_2')->nullable();
            $table->text('user_img_3' ,255)->nullable();
            $table->text('user_title_3')->nullable();
            $table->longText('user_para_3')->nullable();
            $table->text('how_it_work_heading')->nullable(); 
            $table->text('how_it_work_title_1')->nullable(); 
            $table->longText('how_it_work_para_1')->nullable();
            $table->text('how_it_work_img_1' ,255)->nullable();
            $table->text('how_it_work_title_2')->nullable(); 
            $table->longText('how_it_work_para_2')->nullable();
            $table->text('how_it_work_img_2' ,255)->nullable();
            $table->text('how_it_work_title_3')->nullable(); 
            $table->longText('how_it_work_para_3')->nullable();
            $table->text('how_it_work_img_3' ,255)->nullable();
            $table->text('how_it_work_title_4')->nullable(); 
            $table->longText('how_it_work_para_4')->nullable();
            $table->text('how_it_work_img_4' ,255)->nullable();
            $table->text('how_it_work_title_5')->nullable(); 
            $table->longText('how_it_work_para_5')->nullable();
            $table->text('how_it_work_img_5' ,255)->nullable();
            $table->text('how_it_work_title_6')->nullable(); 
            $table->longText('how_it_work_para_6')->nullable();
            $table->text('how_it_work_img_6' ,255)->nullable();
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
        Schema::dropIfExists('landing_users');
    }
};
