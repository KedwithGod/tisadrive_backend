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
        Schema::create('cancellation_reason_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('cancellation_reason_id');
            $table->string('name');
            $table->string('locale'); 
            $table->timestamps();

            $table->foreign('cancellation_reason_id')
                    ->references('id')
                    ->on('cancellation_reasons')
                    ->onDelete('cascade');
        });
        if (Schema::hasTable('cancellation_reasons')) {
            if (!Schema::hasColumn('cancellation_reasons', 'translation_dataset')) {
                Schema::table('cancellation_reasons', function (Blueprint $table) {  
                    $table->text('translation_dataset')->after('reason')->nullable(); 
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellation_reason_translations');
        if (Schema::hasTable('cancellation_reasons')) {
            if (Schema::hasColumn('cancellation_reasons', 'translation_dataset')) {
                Schema::table('cancellation_reasons', function (Blueprint $table) {  
                    $table->dropColumn('translation_dataset');
                });
            }
        }
    }
};
