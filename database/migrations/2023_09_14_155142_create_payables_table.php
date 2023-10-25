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
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('customer_service_id');
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2)->default('0');
            $table->string('gateway_payment');
            $table->string('payment_method');
            $table->date('date_invoice');
            $table->date('date_due');
            $table->date('date_payment')->nullable();
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['Pendente','Processamento','Pago','Cancelado']);
            $table->text('image_url_pix')->nullable();
            $table->text('pix_digitable')->nullable();
            $table->longText('qrcode_pix_base64')->nullable();
            $table->text('billet_url')->nullable();
            $table->longText('billet_base64')->nullable();
            $table->text('billet_digitable')->nullable();
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
        Schema::dropIfExists('jobs');
    }
};
