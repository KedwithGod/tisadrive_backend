<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('subscription_details')){
        Schema::create('subscription_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('subscription_id');
            $table->unsignedInteger('driver_id');
            $table->string('transaction_id')->nullable();
            $table->integer('payment_opt')->comment('0 => card,1 => cash,2 => wallet,3=>wallet/cash');
            $table->double('amount');
            $table->timestamp('expired_at');
            $table->integer('subscription_type')->default(0);
            $table->timestamps();

            $table->foreign('driver_id')
                    ->references('id')
                    ->on('drivers')
                    ->onDelete('cascade');
            $table->foreign('subscription_id')
                    ->references('id')
                    ->on('subscriptions')
                    ->onDelete('cascade');

        });
        }
        
        if (Schema::hasTable('drivers')) {
            if (!Schema::hasColumn('drivers', 'is_subscribed')) {
                Schema::table('drivers', function (Blueprint $table) {
                    $table->boolean('is_subscribed')->default(0)->after('vehicle_type');
                });
            }
            if (!Schema::hasColumn('drivers', 'subscription_detail_id')) {
                Schema::table('drivers', function (Blueprint $table) {
                    $table->uuid('subscription_detail_id')->nullable()->after('is_subscribed');
                    $table->foreign('subscription_detail_id')
                          ->references('id')
                          ->on('subscription_details')
                          ->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('drivers')) {
            if (Schema::hasColumn('drivers', 'is_subscribed')) {
                Schema::table('drivers', function (Blueprint $table) {
                    $table->dropColumn('is_subscribed');
                });
            }
            if (Schema::hasColumn('drivers', 'subscription_detail_id')) {
                Schema::table('drivers', function (Blueprint $table) {
                    $table->dropForeign(['subscription_detail_id']);
                    $table->dropColumn('subscription_detail_id');
                });
            }
        }
        Schema::dropIfExists('subscription_details');
    }
}
