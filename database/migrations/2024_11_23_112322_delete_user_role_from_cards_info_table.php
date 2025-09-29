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
        if (Schema::hasTable('card_info')) {
            if (Schema::hasColumn('card_info', 'user_role')) {
                Schema::table('card_info', function (Blueprint $table) {
                    $table->string('user_role')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
    }
};
