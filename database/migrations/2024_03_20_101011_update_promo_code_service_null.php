<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePromoCodeServiceNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('promo')){
            if(Schema::hasColumn('promo','service_location_id')){
                Schema::table('promo', function (Blueprint $table) {
                    $foreignKeyExists = DB::select(
                        "SELECT CONSTRAINT_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = 'faqs' 
                        AND COLUMN_NAME = 'service_location_id' 
                        AND CONSTRAINT_SCHEMA = DATABASE()"
                    );
                    if($foreignKeyExists){
                        $table->dropForeign(['service_location_id']);
                    }
                });
        
        
                Schema::table('promo', function (Blueprint $table) {
                    $table->unsignedBigInteger('service_location_id')->nullable()->change();
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
        Schema::table('promo', function (Blueprint $table) {
            $table->foreign('service_location_id')->references('id')->on('service_locations')->onDelete('cascade');
        });
    }
}
