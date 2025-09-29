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
        Schema::table('support_ticket_titles', function (Blueprint $table) {
            if (!Schema::hasColumn('support_ticket_titles', 'user_type')) {
                $table->enum('user_type',['user','driver'])->after('title_type')->nullable();                
            }
            $table->longText('category_type')->nullable()->change();
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_ticket_titles', function (Blueprint $table) {
            //
        });
    }
};
