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
            $table->id();
            $table->string('document')->nullable();
            $table->string('company')->nullable();
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('status',['Ativo','Inativo'])->default('Ativo');
            $table->string('image')->nullable();
            $table->string('telephone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('cep')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('api_host_whatsapp')->nullable();
            $table->text('api_access_token_whatsapp')->nullable();
            $table->string('token_paghiper')->nullable();
            $table->string('key_paghiper')->nullable();
            $table->string('access_token_mp')->nullable();
            $table->string('inter_host')->nullable();
            $table->string('inter_client_id')->nullable();
            $table->string('inter_client_secret')->nullable();
            $table->string('inter_scope')->nullable();
            $table->string('inter_grant_type')->nullable();
            $table->string('inter_crt_file')->nullable();
            $table->string('inter_key_file')->nullable();
            $table->string('inter_crt_file_webhook')->nullable();
            $table->string('inter_webhook_url_billet')->nullable();
            $table->string('inter_webhook_url_pix')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
