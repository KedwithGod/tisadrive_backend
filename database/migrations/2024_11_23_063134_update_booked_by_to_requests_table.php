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
        if(Schema::hasTable('requests')){
            if(!Schema::hasColumn('requests','booked_by')){
                Schema::table('requests', function (Blueprint $table) {
                    $table->unsignedInteger('booked_by')->after('dispatcher_id')->nullable();

                    $table->foreign('booked_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('cascade');
                   
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            //
        });
    }
};
