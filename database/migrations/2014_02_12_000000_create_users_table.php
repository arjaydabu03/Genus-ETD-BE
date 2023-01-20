<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_code')->unique();
            $table->string('account_name')->unique();
            $table->string('location_code');
            $table->string('location');
            $table->string('department_code');
            $table->string('department');
            $table->string('company_code');
            $table->string('company');
            $table->string('scope_id');
            $table->string('type')->nullable();
            $table->string('mobile_no');
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
