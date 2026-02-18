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
        Schema::table('users', function (Blueprint $table) {
            // Remover campos de WhatsApp
            $table->dropColumn([
                'api_session_whatsapp',
                'api_token_whatsapp',
                'api_status_whatsapp',
            ]);

            // Remover campos de PagHiper
            $table->dropColumn([
                'token_paghiper',
                'key_paghiper',
                'access_token_paghiper',
            ]);

            // Remover campos de Mercado Pago
            $table->dropColumn('access_token_mp');

            // Remover campos de Banco Inter
            $table->dropColumn([
                'inter_host',
                'inter_client_id',
                'inter_client_secret',
                'inter_scope',
                'inter_crt_file',
                'inter_key_file',
                'inter_crt_file_webhook',
                'inter_webhook_url_billet',
                'inter_webhook_url_pix',
                'inter_chave_pix',
                'access_token_inter',
            ]);

            // Remover campos de Asaas
            $table->dropColumn([
                'environment_asaas',
                'at_asaas_prod',
                'at_asaas_test',
                'asaas_url_test',
                'asaas_url_prod',
            ]);

            // Remover campos de configuração de fatura
            $table->dropColumn([
                'day_generate_invoice',
                'send_generate_invoice',
            ]);

            // Remover chave PIX (agora está em companies)
            $table->dropColumn('chave_pix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // WhatsApp
            $table->string('api_session_whatsapp')->nullable();
            $table->text('api_token_whatsapp')->nullable();
            $table->string('api_status_whatsapp')->nullable();

            // PagHiper
            $table->string('token_paghiper')->nullable();
            $table->string('key_paghiper')->nullable();
            $table->string('access_token_paghiper')->nullable();

            // Mercado Pago
            $table->string('access_token_mp')->nullable();

            // Banco Inter
            $table->string('inter_host')->default('https://cdpj.partners.bancointer.com.br/')->nullable();
            $table->string('inter_client_id')->nullable();
            $table->string('inter_client_secret')->nullable();
            $table->string('inter_scope')->default('boleto-cobranca.read boleto-cobranca.write extrato.read cob.write cob.read cobv.write cobv.read pix.write pix.read webhook.read webhook.write')->nullable();
            $table->string('inter_crt_file')->nullable();
            $table->string('inter_key_file')->nullable();
            $table->string('inter_crt_file_webhook')->nullable();
            $table->string('inter_webhook_url_billet')->default('https://cobrancasegura.com.br/webhook/intermediumbillet')->nullable();
            $table->string('inter_webhook_url_pix')->default('https://cobrancasegura.com.br/webhook/intermediumpix')->nullable();
            $table->text('inter_chave_pix')->nullable();
            $table->string('access_token_inter')->nullable();

            // Asaas
            $table->enum('environment_asaas', ['Teste', 'Produção'])->default('Teste')->nullable();
            $table->string('at_asaas_prod')->nullable();
            $table->string('at_asaas_test')->nullable();
            $table->string('asaas_url_test')->default('https://sandbox.asaas.com/api/')->nullable();
            $table->string('asaas_url_prod')->default('https://api.asaas.com/')->nullable();

            // Configurações de fatura
            $table->integer('day_generate_invoice')->nullable();
            $table->enum('send_generate_invoice', ['Não', 'Sim'])->nullable();

            // Chave PIX
            $table->text('chave_pix')->nullable();
        });
    }
};
