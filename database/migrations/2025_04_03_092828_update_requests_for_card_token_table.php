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
            if (!Schema::hasColumn('requests', 'card_token')) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->string('card_token')->after('payment_opt')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
