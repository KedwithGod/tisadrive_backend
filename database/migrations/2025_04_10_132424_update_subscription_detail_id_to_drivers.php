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
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasTable('drivers')) {
                if (Schema::hasColumn('drivers', 'subscription_detail_id')) {
                    Schema::table('drivers', function (Blueprint $table) {
                        $foreignKeyExists = DB::select(
                            "SELECT CONSTRAINT_NAME 
                            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                            WHERE TABLE_NAME = 'drivers' 
                            AND COLUMN_NAME = 'subscription_detail_id' 
                            AND CONSTRAINT_SCHEMA = DATABASE()"
                        );
                        if($foreignKeyExists){
                            $table->dropForeign(['subscription_detail_id']);
                        }
                    });
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('drivers')) {
            if (Schema::hasColumn('drivers', 'subscription_detail_id')) {
                Schema::table('drivers', function (Blueprint $table) {
                    $foreignKeyExists = DB::select(
                        "SELECT CONSTRAINT_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = 'drivers' 
                        AND COLUMN_NAME = 'subscription_detail_id' 
                        AND CONSTRAINT_SCHEMA = DATABASE()"
                    );
                    if(!$foreignKeyExists){
                        $table->foreign('subscription_detail_id')
                        ->references('id')
                        ->on('subscription_details')
                        ->onDelete('cascade');
                    }
                });
            }
        }
    }
};
