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
        
        if (Schema::hasTable('driver_needed_documents')) {
            Schema::table('driver_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('driver_needed_documents', 'document_name_front')) {
                    $table->string('document_name_front')->after('name')->nullable();
                }
            });
        
            Schema::table('driver_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('driver_needed_documents', 'document_name_back')) {
                    $table->string('document_name_back')->after('document_name_front')->nullable();
                }
            });
        }
        if (Schema::hasTable('owner_needed_documents')) {
            Schema::table('owner_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('owner_needed_documents', 'document_name_front')) {
                    $table->string('document_name_front')->after('name')->nullable();
                }
            });
        
            Schema::table('owner_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('owner_needed_documents', 'document_name_back')) {
                    $table->string('document_name_back')->after('document_name_front')->nullable();
                }
            });
        }
        if (Schema::hasTable('fleet_needed_documents')) {
            Schema::table('fleet_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('fleet_needed_documents', 'document_name_front')) {
                    $table->string('document_name_front')->after('name')->nullable();
                }
            });
        
            Schema::table('fleet_needed_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('fleet_needed_documents', 'document_name_back')) {
                    $table->string('document_name_back')->after('document_name_front')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('driver_needed_documents')) {
            Schema::table('driver_needed_documents', function (Blueprint $table) {
                if (Schema::hasColumn('driver_needed_documents', 'document_name_front')) {
                    $table->dropColumn('document_name_front');
                }
            });
        
            Schema::table('driver_needed_documents', function (Blueprint $table) {
                if (Schema::hasColumn('driver_needed_documents', 'document_name_back')) {
                    $table->dropColumn('document_name_back');
                }
            });
        }
        if (Schema::hasTable('fleet_needed_documents')) {
            Schema::table('fleet_needed_documents', function (Blueprint $table) {
                if (Schema::hasColumn('fleet_needed_documents', 'document_name_front')) {
                    $table->dropColumn('document_name_front');
                }
            });
        
            Schema::table('fleet_needed_documents', function (Blueprint $table) {
                if (Schema::hasColumn('fleet_needed_documents', 'document_name_back')) {
                    $table->dropColumn('document_name_back');
                }
            });
        }
        if (Schema::hasTable('owner_needed_documents')) {
            Schema::table('owner_needed_documents', function (Blueprint $table) {
                if (Schema::hasColumn('owner_needed_documents', 'document_name_front')) {
                    $table->dropColumn('document_name_front');
                }
            });
        
            Schema::table('owner_needed_documents', function (Blueprint $table) {
                if (Schema::hasColumn('owner_needed_documents', 'document_name_back')) {
                    $table->dropColumn('document_name_back');
                }
            });
        }
    }
};
