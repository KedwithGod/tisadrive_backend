<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeakZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peak_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('zone_id');
            $table->double('lat', 15, 8)->nullable();
            $table->double('lng', 15, 8)->nullable();
            $table->string('name')->nullable();
            $table->multiPolygon('coordinates')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('distance_price_percentage')->comment('In percentage');

            $table->boolean('active')->default(true);

            $table->foreign('zone_id')
                    ->references('id')
                    ->on('zones')
                    ->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peak_zones');
    }
}
