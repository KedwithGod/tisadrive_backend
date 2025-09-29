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
        if(!Schema::hasTable('owner_bank_infos')){

            Schema::create('owner_bank_infos', function (Blueprint $table) {
                $table->uuid('id')->primary(); // Primary key UUID column
                $table->uuid('owner_id');
                $table->unsignedInteger('method_id'); 
                $table->unsignedInteger('field_id'); 
                $table->string('value')->nullable(); 
                $table->timestamps();
            
                $table->foreign('owner_id')
                      ->references('id')
                      ->on('owners')
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
        Schema::dropIfExists('owner_bank_infos');
    }
};
