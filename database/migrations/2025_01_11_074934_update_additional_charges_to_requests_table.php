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
        if (Schema::hasTable('requests')) {
            
            Schema::table('requests', function (Blueprint $table) {
                if (!Schema::hasColumn('requests', 'additional_charges_reason')) {
                    $table->string('additional_charges_reason')->after('requested_currency_symbol')->nullable();
                }
                if (!Schema::hasColumn('requests', 'additional_charges_amount')) {
                    $table->double('additional_charges_amount', 10, 2)->after('additional_charges_reason')->default(0);
                }
            });

        }

        if (Schema::hasTable('request_bills')) {
            
            Schema::table('request_bills', function (Blueprint $table) {
                if (!Schema::hasColumn('request_bills', 'additional_charges_reason')) {
                    $table->string('additional_charges_reason')->after('requested_currency_symbol')->nullable();
                }
                if (!Schema::hasColumn('request_bills', 'additional_charges_amount')) {
                    $table->double('additional_charges_amount', 10, 2)->after('additional_charges_reason')->default(0);
                }
            });

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('requests')) {
            if (Schema::hasColumn('requests', 'additional_charges_reason')) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->dropColumn('additional_charges_reason');
                });
            }
            if (Schema::hasColumn('requests', 'additional_charges_amount')) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->dropColumn('additional_charges_amount');
                });
            }
        }

        if (Schema::hasTable('request_bills')) {
            if (Schema::hasColumn('request_bills', 'additional_charges_reason')) {
                Schema::table('request_bills', function (Blueprint $table) {
                    $table->dropColumn('additional_charges_reason');
                });
            }
            if (Schema::hasColumn('request_bills', 'additional_charges_amount')) {
                Schema::table('request_bills', function (Blueprint $table) {
                    $table->dropColumn('additional_charges_amount');
                });
            }
        }
    }
};
