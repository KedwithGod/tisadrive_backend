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
        Schema::create('goods_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('goods_type_id');
            $table->string('name'); 
            $table->string('locale'); 
            $table->timestamps();

            $table->foreign('goods_type_id')
                    ->references('id')
                    ->on('goods_types')
                    ->onDelete('cascade');
        });
        if (Schema::hasTable('goods_types')) {
            if (!Schema::hasColumn('goods_types', 'translation_dataset')) {
                Schema::table('goods_types', function (Blueprint $table) {  
                    $table->text('translation_dataset')->after('goods_type_name')->nullable(); 
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_type_translations');
        if (Schema::hasTable('goods_types')) {
            if (Schema::hasColumn('goods_types', 'translation_dataset')) {
                Schema::table('goods_types', function (Blueprint $table) {  
                    $table->dropColumn('translation_dataset');
                });
            }
        }
    }
};
