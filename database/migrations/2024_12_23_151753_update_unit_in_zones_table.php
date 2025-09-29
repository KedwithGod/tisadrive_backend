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
        if(Schema::hasTable('zones')) {
            if(Schema::hasColumn('zones','unit')) {
                Schema::table('zones', function (Blueprint $table) {
                    $table->enum('unit', [1,2])->comment('1 => kilometers,2 => miles')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasTable('zones')) {
            if(Schema::hasColumn('zones','unit')) {
                Schema::table('zones', function (Blueprint $table) {
                    $table->tinyInteger('unit')->change();
                });
            }
        }
    }
};
