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
        Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id');
        $table->string('name');
        $table->enum('type',['Física','Jurídica']);
        $table->string('document');
        $table->string('company')->nullable();
        $table->string('email')->nullable();
        $table->string('email2')->nullable();
        $table->enum('status', ['Ativo','Inativo']);
        $table->string('cep')->nullable();
        $table->string('address')->nullable();
        $table->string('number')->nullable();
        $table->string('complement')->nullable();
        $table->string('district')->nullable();
        $table->string('city')->nullable();
        $table->string('state')->nullable();
        $table->string('phone')->nullable();
        $table->string('whatsapp')->nullable();
        $table->enum('gateway_pix',['Pag Hiper','Mercado Pago']);
        $table->enum('gateway_billet',['Pag Hiper','Mercado Pago']);
        $table->enum('gateway_card',['PagHiper','Mercado Pago']);
        $table->enum('payment_method',['Pix','Boleto','Depósito','Dinheiro','Cartão']);
        $table->char('notification_whatsapp')->default('s');
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
        Schema::dropIfExists('customers');
    }
};
