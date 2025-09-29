<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('user_id');
            $table->double('points_added', 10, 2)->default(0);
            $table->double('points_spend', 10, 2)->default(0);
            $table->double('balance_reward_points', 10, 2)->default(0);
            $table->double('total_reward_points_collected', 10, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
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
        Schema::dropIfExists('reward_points');
    }
}
