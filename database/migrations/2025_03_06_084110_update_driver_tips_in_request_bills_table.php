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
        if (Schema::hasTable('request_bills')) {
            if(!Schema::hasColumn('request_bills','tips')) {
                Schema::table('request_bills', function (Blueprint $table) {
                    $table->double('tips',10,2)->default(0)->after('driver_commision');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('request_bills')) {
            if(!Schema::hasColumn('request_bills','tips')) {
                Schema::table('request_bills', function (Blueprint $table) {
                    $table->dropColumn('tips');
                });
            }
        }
    }
};
