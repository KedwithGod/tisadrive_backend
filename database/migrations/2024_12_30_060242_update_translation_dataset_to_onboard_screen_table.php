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
    
        if (Schema::hasTable('onboarding_screen')) {
            
            if (!Schema::hasColumn('onboarding_screen', 'translation_dataset')) {
                Schema::table('onboarding_screen', function (Blueprint $table) {
                    $table->text('translation_dataset')->after('description')->nullable();
                });
            }

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboard_screen', function (Blueprint $table) {
            //
        });
    }
};
