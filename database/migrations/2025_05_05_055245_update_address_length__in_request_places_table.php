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
        if(Schema::hasTable('request_places')){
            if(Schema::hasColumn('request_places','pick_address')){
                Schema::table('request_places', function (Blueprint $table) {
                    $table->text('pick_address')->nullable()->change();
                });
            }
            if(Schema::hasColumn('request_places','drop_address')){
                Schema::table('request_places', function (Blueprint $table) {
                    $table->text('drop_address')->nullable()->change();
                });
            }
        }
        if(Schema::hasTable('favourite_locations')){
            if(Schema::hasColumn('favourite_locations','pick_address')){
                Schema::table('favourite_locations', function (Blueprint $table) {
                    $table->text('pick_address')->nullable()->change();
                });
            }
            if(Schema::hasColumn('favourite_locations','drop_address')){
                Schema::table('favourite_locations', function (Blueprint $table) {
                    $table->text('drop_address')->nullable()->change();
                });
            }
        }
        if(Schema::hasTable('recent_searches')){
            if(Schema::hasColumn('recent_searches','pick_address')){
                Schema::table('recent_searches', function (Blueprint $table) {
                    $table->text('pick_address')->nullable()->change();
                });
            }
            if(Schema::hasColumn('recent_searches','drop_address')){
                Schema::table('recent_searches', function (Blueprint $table) {
                    $table->text('drop_address')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('request_places')){
            if(Schema::hasColumn('request_places','pick_address')){
                Schema::table('request_places', function (Blueprint $table) {
                    $table->string('pick_address')->nullable()->change();
                });
            }
            if(Schema::hasColumn('request_places','drop_address')){
                Schema::table('request_places', function (Blueprint $table) {
                    $table->string('drop_address')->nullable()->change();
                });
            }
        }
        if(Schema::hasTable('favourite_locations')){
            if(Schema::hasColumn('favourite_locations','pick_address')){
                Schema::table('favourite_locations', function (Blueprint $table) {
                    $table->string('pick_address')->nullable()->change();
                });
            }
            if(Schema::hasColumn('favourite_locations','drop_address')){
                Schema::table('favourite_locations', function (Blueprint $table) {
                    $table->string('drop_address')->nullable()->change();
                });
            }
        }
        if(Schema::hasTable('recent_searches')){
            if(Schema::hasColumn('recent_searches','pick_address')){
                Schema::table('recent_searches', function (Blueprint $table) {
                    $table->string('pick_address')->nullable()->change();
                });
            }
            if(Schema::hasColumn('recent_searches','drop_address')){
                Schema::table('recent_searches', function (Blueprint $table) {
                    $table->string('drop_address')->nullable()->change();
                });
            }
        }
    }
};
