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
        
        if (Schema::hasTable('service_locations')) {
            if (!Schema::hasColumn('service_locations', 'translation_dataset')) {
        Schema::table('service_locations', function (Blueprint $table) {  
              $table->text('translation_dataset')->after('name')->nullable(); 
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
