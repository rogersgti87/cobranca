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
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['owner', 'admin', 'user'])->default('user'); // Papel do usuário na empresa
            $table->timestamps();

            // Foreign keys - sem cascade delete em users para evitar problemas
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('user_id'); // Apenas índice, sem foreign key

            // Índices únicos para evitar duplicatas
            $table->unique(['company_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_user');
    }
};
