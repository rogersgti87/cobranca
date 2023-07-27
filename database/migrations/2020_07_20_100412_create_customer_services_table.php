<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_services', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('customer_id');
            $table->integer('service_id');
            $table->text('description')->nullable();
            $table->date('date_due')->nullable();
            $table->decimal('price', 10, 2)->default('0');
            $table->string('period');
            $table->enum('status', ['Ativo','Inativo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_services');
    }
}
