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
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->index();
                $table->string('name');
                $table->enum('type', ['Física', 'Jurídica'])->default('Física');
                $table->string('document', 20)->nullable();
                $table->string('company')->nullable();
                $table->string('email')->nullable();
                $table->string('email2')->nullable();
                $table->enum('status', ['Ativo', 'Inativo'])->default('Ativo')->index();
                $table->string('cep', 10)->nullable();
                $table->string('address')->nullable();
                $table->string('number', 10)->nullable();
                $table->string('complement')->nullable();
                $table->string('district')->nullable();
                $table->string('city')->nullable();
                $table->string('state', 2)->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('whatsapp', 20)->nullable();
                $table->text('obs')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
