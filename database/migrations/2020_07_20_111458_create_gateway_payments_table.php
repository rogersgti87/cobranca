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
        if (!Schema::hasTable('gateway_payments')) {
            Schema::create('gateway_payments', function (Blueprint $table) {
                $table->id('id');
                $table->string('name');
                $table->char('Boleto')->default('n');
                $table->char('Pix')->default('n');
                $table->char('BoletoPix')->default('n');
                $table->char('Cartão')->default('n');
                $table->char('Dinheiro')->default('n');
                $table->char('Depósito')->default('n');
                $table->enum('status', ['Ativo','Inativo']);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateway_payments');
    }


};
