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
        if(!Schema::hasTable('driver_bank_infos')){

            Schema::create('driver_bank_infos', function (Blueprint $table) {
                $table->uuid('id')->primary(); // Primary key UUID column
                $table->unsignedInteger('driver_id');
                $table->unsignedInteger('method_id'); 
                $table->unsignedInteger('field_id'); 
                $table->string('value')->nullable(); 
                $table->timestamps();
            
                $table->foreign('driver_id')
                      ->references('id')
                      ->on('drivers')
                      ->onDelete('cascade');
                $table->foreign('method_id')
                      ->references('id')
                      ->on('methods')
                      ->onDelete('cascade');
                $table->foreign('field_id')
                      ->references('id')
                      ->on('fields')
                      ->onDelete('cascade');
            });
        }
        
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_bank_infos');
    }
};
