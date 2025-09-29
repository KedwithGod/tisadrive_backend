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
        Schema::create('fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('method_id'); 
            $table->string('input_field_name');
            $table->string('placeholder')->nullable();
            $table->boolean('is_required')->default(false);
            $table->string('input_field_type');
            $table->timestamps();

            $table->foreign('method_id')
                    ->references('id')
                    ->on('methods')
                    ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
