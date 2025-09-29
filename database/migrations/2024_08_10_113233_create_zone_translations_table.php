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
        Schema::create('zone_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('zone_id');
            $table->string('name');
            $table->string('locale'); 
            $table->timestamps();

            $table->foreign('zone_id')
                    ->references('id')
                    ->on('zones')
                    ->onDelete('cascade');
        });
        if (Schema::hasTable('zones')) {
            if (!Schema::hasColumn('zones', 'translation_dataset')) {
                Schema::table('zones', function (Blueprint $table) {  
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
        Schema::dropIfExists('zone_translations');
        if (Schema::hasTable('zones')) {
            if (Schema::hasColumn('zones', 'translation_dataset')) {
                Schema::table('zones', function (Blueprint $table) {  
                    $table->dropColumn('translation_dataset');
                });
            }
        }
    }
};
