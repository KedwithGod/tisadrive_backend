<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersBankInfoNullableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_bank_info', function (Blueprint $table) {
            // Make foreign key columns nullable
            $table->unsignedInteger('user_id')->nullable()->change();
            
            // Make string columns nullable
            $table->string('account_name')->nullable()->change();
            $table->string('account_no')->nullable()->change();
            $table->string('bank_code')->nullable()->change();
            $table->string('bank_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_bank_info', function (Blueprint $table) {
        });
    }
}
