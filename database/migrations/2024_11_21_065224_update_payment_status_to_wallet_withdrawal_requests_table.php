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
        if (Schema::hasTable('wallet_withdrawal_requests')) {
            // Add image_type column if it doesn't already exist
            Schema::table('wallet_withdrawal_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('wallet_withdrawal_requests', 'payment_status')) {
                    $table->string('payment_status')->after('status')->nullable();
                }
            });
        
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_withdrawal_requests', function (Blueprint $table) {
            //
        });
    }
};
