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
        Schema::create('recent_searches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->double('pick_lat', 15, 8)->nullable();
            $table->double('pick_lng', 15, 8)->nullable();
            $table->double('drop_lat', 15, 8)->nullable();
            $table->double('drop_lng', 15, 8)->nullable();
            $table->string('pick_address')->nullable();
            $table->string('pick_short_address')->nullable();
            $table->string('drop_address')->nullable();
            $table->string('drop_short_address')->nullable();
            $table->string('pickup_poc_name')->nullable();
            $table->string('pickup_poc_mobile', 14)->nullable();
            $table->string('pickup_poc_instruction', 14)->nullable();
            $table->string('drop_poc_name', 14)->nullable();
            $table->string('drop_poc_mobile', 14)->nullable();
            $table->string('drop_poc_instruction')->nullable();
            $table->double('total_distance', 15, 2)->default(0);
            $table->double('total_time', 15, 2)->default(0);
            $table->longText('poly_line')->nullable();
            $table->timestamps();


            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recent_searches');
    }
};
