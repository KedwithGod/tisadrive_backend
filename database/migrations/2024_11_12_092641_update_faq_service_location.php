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
        if (Schema::hasTable('faqs')) {
            if (Schema::hasColumn('faqs', 'service_location_id')) {
                Schema::table('faqs', function (Blueprint $table) {
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
                    $table->uuid('service_location_id')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('faqs')) {
            if (Schema::hasColumn('faqs', 'service_location_id')) {
                Schema::table('faqs', function (Blueprint $table) {
                    $foreignKeyExists = DB::select(
                        "SELECT CONSTRAINT_NAME 
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = 'faqs' 
                        AND COLUMN_NAME = 'service_location_id' 
                        AND CONSTRAINT_SCHEMA = DATABASE()"
                    );
                    if(!$foreignKeyExists){
                        $table->foreign('service_location_id')
                            ->references('id')
                            ->on('service_locations')
                            ->onDelete('cascade');
                    }
                    $table->uuid('service_location_id')->change();
                });
            }
        }
    }
};
