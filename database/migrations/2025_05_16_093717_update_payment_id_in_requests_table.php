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
           if (!Schema::hasColumn('requests', 'payment_intent_id')) {
               Schema::table('requests', function (Blueprint $table) {
                   $table->string('payment_intent_id')->after('payment_opt')->nullable();
               });
           }
       }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('requests')) {
           if (Schema::hasColumn('requests', 'payment_intent_id')) {
               Schema::table('requests', function (Blueprint $table) {
                   $table->dropColumn('payment_intent_id');
               });
           }
       }
    }
};
