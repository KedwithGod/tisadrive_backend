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
        Schema::create('landing_drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('hero_title')->nullable();
            $table->text('driver_heading_1')->nullable();
            $table->longText('driver_para')->nullable();
            $table->text('driver_img_1' ,255)->nullable();
            $table->text('driver_title_1')->nullable();
            $table->longText('driver_para_1')->nullable();
            $table->text('driver_img_2' ,255)->nullable();
            $table->text('driver_title_2')->nullable();
            $table->longText('driver_para_2')->nullable();
            $table->text('driver_img_3' ,255)->nullable();
            $table->text('driver_title_3')->nullable();
            $table->longText('driver_para_3')->nullable();
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
            $table->text('how_it_work_title_7')->nullable(); 
            $table->longText('how_it_work_para_7')->nullable();
            $table->text('how_it_work_img_7' ,255)->nullable();
            $table->text('req_heading')->nullable();
            $table->text('req_title')->nullable();
            $table->longText('req_lists')->nullable();
            $table->text('req_img' ,255)->nullable();
            $table->text('vechile_req_title')->nullable();
            $table->longText('vechile_req_lists')->nullable();
            $table->text('vechile_req_img' ,255)->nullable();
            $table->text('doc_req_title')->nullable();
            $table->longText('doc_req_lists')->nullable();
            $table->text('doc_req_img' ,255)->nullable();
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
        Schema::dropIfExists('landing_drivers');
    }
};
