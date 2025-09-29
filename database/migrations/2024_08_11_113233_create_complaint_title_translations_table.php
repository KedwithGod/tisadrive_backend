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
        Schema::create('complaint_title_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('complaint_title_id');
            $table->string('name');
            $table->string('locale'); 
            $table->timestamps();

            $table->foreign('complaint_title_id')
                    ->references('id')
                    ->on('complaint_titles')
                    ->onDelete('cascade');
        });
        if (Schema::hasTable('complaint_titles')) {
            if (!Schema::hasColumn('complaint_titles', 'translation_dataset')) {
                Schema::table('complaint_titles', function (Blueprint $table) {  
                    $table->text('translation_dataset')->after('title')->nullable(); 
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_title_translations');
        if (Schema::hasTable('complaint_titles')) {
            if (Schema::hasColumn('complaint_titles', 'translation_dataset')) {
                Schema::table('complaint_titles', function (Blueprint $table) {  
                    $table->dropColumn('translation_dataset');
                });
            }
        }
    }
};
