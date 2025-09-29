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
        Schema::create('landing_quicklinks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('privacy_title')->nullable();
            $table->longText('privacy')->nullable();
            $table->text('terms_title')->nullable();
            $table->longText('terms')->nullable();
            $table->text('compliance_title')->nullable();
            $table->longText('compliance')->nullable();
            $table->text('dmv_title')->nullable();
            $table->longText('dmv')->nullable();
            $table->text('locale')->nullable();
            $table->text('language')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_quicklinks');
    }
};
