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
        $table->date('birthdate')->nullable();
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
        $table->char('notification_whatsapp')->default('s');
        $table->text('obs')->nullable();
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
