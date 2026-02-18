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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da empresa
            $table->enum('type', ['Física', 'Jurídica'])->default('Jurídica');
            $table->string('document', 20)->nullable()->unique(); // CNPJ ou CPF
            $table->string('trade_name')->nullable(); // Nome fantasia
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            
            // Endereço
            $table->string('cep', 10)->nullable();
            $table->string('address')->nullable();
            $table->string('number', 10)->nullable();
            $table->string('complement')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            
            // Configurações de pagamento
            $table->text('chave_pix')->nullable();
            
            // Integrações PagHiper
            $table->string('token_paghiper')->nullable();
            $table->string('key_paghiper')->nullable();
            $table->string('access_token_paghiper')->nullable();
            
            // Integrações Mercado Pago
            $table->string('access_token_mp')->nullable();
            
            // Integrações Banco Inter
            $table->string('inter_host')->default('https://cdpj.partners.bancointer.com.br/')->nullable();
            $table->string('inter_client_id')->nullable();
            $table->string('inter_client_secret')->nullable();
            $table->string('inter_scope')->default('boleto-cobranca.read boleto-cobranca.write  extrato.read cob.write cob.read cobv.write cobv.read pix.write pix.read webhook.read webhook.write')->nullable();
            $table->string('inter_crt_file')->nullable();
            $table->string('inter_key_file')->nullable();
            $table->string('inter_crt_file_webhook')->nullable();
            $table->string('inter_webhook_url_billet')->default('https://cobrancasegura.com.br/webhook/intermediumbillet')->nullable();
            $table->string('inter_webhook_url_pix')->default('https://cobrancasegura.com.br/webhook/intermediumpix')->nullable();
            $table->text('inter_chave_pix')->nullable();
            $table->string('access_token_inter')->nullable();
            $table->char('use_intermedium', 1)->default('n')->nullable();
            
            // Integrações Asaas
            $table->enum('environment_asaas', ['Teste', 'Produção'])->default('Teste')->nullable();
            $table->string('at_asaas_prod')->nullable();
            $table->string('at_asaas_test')->nullable();
            $table->string('asaas_url_test')->default('https://sandbox.asaas.com/api/')->nullable();
            $table->string('asaas_url_prod')->default('https://api.asaas.com/')->nullable();
            
            // Integrações WhatsApp
            $table->string('api_session_whatsapp')->nullable();
            $table->text('api_token_whatsapp')->nullable();
            $table->string('api_status_whatsapp')->nullable();
            
            // Configurações de fatura
            $table->integer('day_generate_invoice')->nullable();
            $table->enum('send_generate_invoice', ['Não', 'Sim'])->nullable();
            
            // Typebot
            $table->string('typebot_id')->nullable();
            $table->char('typebot_enable', 1)->default('n')->nullable();
            
            // Logo
            $table->string('logo')->nullable();
            
            $table->enum('status', ['Ativo', 'Inativo'])->default('Ativo');
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
        Schema::dropIfExists('companies');
    }
};
