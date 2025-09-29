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
        if (!Schema::hasTable('support_ticket_titles_translations')) {
            Schema::create('support_ticket_titles_translations', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('ticket_title_id');
                $table->string('title'); 
                $table->string('locale'); 
                $table->timestamps();

                $table->foreign('ticket_title_id')
                    ->references('id')
                    ->on('support_ticket_titles')
                    ->onDelete('cascade');
            });
        }

        if (Schema::hasTable('support_ticket_titles')) {
            if (!Schema::hasColumn('support_ticket_titles', 'translation_dataset')) {
                Schema::table('support_ticket_titles', function (Blueprint $table) {  
                    $table->text('translation_dataset')->after('category_type')->nullable(); 
                });
            }
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_titles_translations');
    }
};
