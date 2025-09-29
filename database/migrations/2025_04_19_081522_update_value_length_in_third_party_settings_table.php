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
        if(Schema::hasTable('third_party_settings')){
            if(Schema::hasColumn('third_party_settings','value')){
                Schema::table('third_party_settings', function (Blueprint $table) {
                    $table->text('value')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('third_party_settings')){
            if(Schema::hasColumn('third_party_settings','value')){
                Schema::table('third_party_settings', function (Blueprint $table) {
                    $table->string('value')->nullable()->change();
                });
            }
        }
    }
};
