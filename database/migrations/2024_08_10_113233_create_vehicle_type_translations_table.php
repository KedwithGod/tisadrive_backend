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
        Schema::create('vehicle_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('vehicle_type_id');
            $table->string('name');
            $table->string('locale'); 
            $table->timestamps();

            $table->foreign('vehicle_type_id')
                    ->references('id')
                    ->on('vehicle_types')
                    ->onDelete('cascade');
        });
        if (Schema::hasTable('vehicle_types')) {
            if (!Schema::hasColumn('vehicle_types', 'translation_dataset')) {
                Schema::table('vehicle_types', function (Blueprint $table) {  
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
        Schema::dropIfExists('vehicle_type_translations');
        if (Schema::hasTable('vehicle_types')) {
            if (Schema::hasColumn('vehicle_types', 'translation_dataset')) {
                Schema::table('vehicle_types', function (Blueprint $table) {  
                    $table->dropColumn('translation_dataset');
                });
            }
        }
    }
};
