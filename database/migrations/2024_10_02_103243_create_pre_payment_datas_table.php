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
        Schema::create('pre_payment_datas', function (Blueprint $table) {
            $table->uuid('id');
            $table->unsignedInteger('driver_id');
            $table->uuid('request_id');
            $table->string('card_number')->nullable();
            $table->string('payment_opt')->nullable();
            $table->double('amount', 10,2)->default(0);
            $table->double('driver_commission', 10,2)->default(0);
            $table->timestamps();

            $table->foreign('driver_id')
                    ->references('id')
                    ->on('drivers')
                    ->onDelete('cascade');
          
            $table->foreign('request_id')
                    ->references('id')
                    ->on('requests')
                    ->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_payment_datas');
    }
};
