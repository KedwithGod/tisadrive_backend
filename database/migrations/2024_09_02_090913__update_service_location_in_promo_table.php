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
        if(Schema::hasTable('promo')){
            if(Schema::hasColumn('promo','service_location_id')){
                Schema::table('promo', function (Blueprint $table) {
                    $foreignKeyExists = DB::select(
                        "SELECT CONSTRAINT_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = 'promo' 
                        AND COLUMN_NAME = 'service_location_id' 
                        AND CONSTRAINT_SCHEMA = DATABASE()"
                    );
                    $table->uuid('service_location_id')->nullable()->change();
                    if(!$foreignKeyExists){
                        $table->foreign('service_location_id')
                            ->references('id')
                            ->on('service_locations')
                            ->onDelete('cascade');
                    }
                });
            }
            if(Schema::hasColumn('promo','promo_code_users_availabe')){
                Schema::table('promo', function (Blueprint $table) {
                    $table->dropColumn('promo_code_users_availabe');
                });
            }
            if(Schema::hasColumn('promo','promo_code_users_available')){
                Schema::table('promo', function (Blueprint $table) {
                    $table->dropColumn('promo_code_users_available');
                });
            }
            if(!Schema::hasColumn('promo','user_specific')){
                Schema::table('promo', function (Blueprint $table) {
                    $table->boolean('user_specific')->after('code')->default(false);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('promo')){
            if(Schema::hasColumn('promo','service_location_id')){
                Schema::table('promo', function (Blueprint $table) {
                    $foreignKeyExists = DB::select(
                        "SELECT CONSTRAINT_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = 'promo' 
                        AND COLUMN_NAME = 'service_location_id' 
                        AND CONSTRAINT_SCHEMA = DATABASE()"
                    );
                    if($foreignKeyExists){
                        $table->dropForeign(['service_location_id']);
                    }
                });
            }
        }
        if(!Schema::hasColumn('promo','promo_code_users_availabe')){
            Schema::table('promo', function (Blueprint $table) {
                $table->enum('promo_code_users_availabe', ['yes','no'])->after('to')->nullable();
            });
        }
        if(Schema::hasColumn('promo','user_specific')){
            Schema::table('promo', function (Blueprint $table) {
                $table->dropColumn('user_specific')->default(false);
            });
        }
        
    }
};
