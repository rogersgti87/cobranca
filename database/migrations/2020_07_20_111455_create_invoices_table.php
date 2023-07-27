<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->integer('customer_service_id');
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2)->default('0');
            $table->string('payment_method');
            $table->date('date_invoice');
            $table->date('date_due');
            $table->date('date_payment')->nullable();
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['Pendente','Processamento','Pago','Cancelado']);
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
        Schema::dropIfExists('invoices');
    }
}
