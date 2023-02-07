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
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_no');
            $table->dateTime('date_ordered');
            $table->date('date_needed');
            $table->string('date_approved')->nullable();
            $table->integer('company_id');
            $table->string('company_name');
            $table->integer('department_id');
            $table->string('department_name');
            $table->integer('location_id');
            $table->string('location_name');
            $table->string('customer_code');
            $table->string('customer_name');
            $table->string('material_code');
            $table->string('material_name');
            $table->integer('category_id');
            $table->string('category_name');
            $table->double('quantity');
            $table->string('remarks')->nullable();
            $table->boolean('is_approved')->default(0);
        
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
        Schema::dropIfExists('order');
    }
};
