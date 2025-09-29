<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('user_id');
            $table->uuid('request_id')->nullable();
            $table->boolean('is_credit')->default(0);
            $table->double('amount', 10, 2)->default(0);
            $table->double('reward_points',10, 2)->default(0);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->foreign('request_id')
                    ->references('id')
                    ->on('requests')
                    ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_histories');
    }
}
