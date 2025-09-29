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
        Schema::create('landing_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('hero_title')->nullable();
            $table->text('contact_heading')->nullable();
            $table->longText('contact_para')->nullable();
            $table->text('contact_address_title')->nullable();
            $table->longText('contact_address')->nullable();
            $table->text('contact_phone_title')->nullable();
            $table->text('contact_phone')->nullable();
            $table->text('contact_mail_title')->nullable();
            $table->text('contact_mail')->nullable();
            $table->text('contact_web_title')->nullable();
            $table->text('contact_web')->nullable();
            $table->text('form_name')->nullable();
            $table->text('form_mail')->nullable();
            $table->text('form_subject')->nullable();
            $table->text('form_message')->nullable();
            $table->text('form_btn')->nullable();
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
        Schema::dropIfExists('landing_contacts');
    }
};
