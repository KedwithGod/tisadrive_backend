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
        Schema::table('chat_messages', function (Blueprint $table) {
                        $table->string('audio_url')->nullable()->after('image_url');
            $table->enum('type', ['text', 'image', 'audio'])->default('text')->after('audio_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
             $table->dropColumn('audio_url');
            $table->dropColumn('type');
        });
    }
};
