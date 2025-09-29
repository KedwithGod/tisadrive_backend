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
        if (Schema::hasTable('requests')) {
            if (!Schema::hasColumn('requests', 'parcel_type')) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->string('parcel_type')->after('is_parcel')->nullable();
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
            $table->dropColumn('parcel_type');
        });
    }
};
