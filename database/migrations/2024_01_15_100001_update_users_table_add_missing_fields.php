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
            // Verificar se as colunas já existem antes de adicionar
            if (!Schema::hasColumn('users', 'api_session_whatsapp')) {
                $table->string('api_session_whatsapp')->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'api_token_whatsapp')) {
                $table->text('api_token_whatsapp')->nullable()->after('api_session_whatsapp');
            }
            if (!Schema::hasColumn('users', 'inter_chave_pix')) {
                $table->text('inter_chave_pix')->nullable()->after('inter_webhook_url_pix');
            }
            if (!Schema::hasColumn('users', 'day_generate_invoice')) {
                $table->integer('day_generate_invoice')->nullable()->after('inter_chave_pix');
            }
            if (!Schema::hasColumn('users', 'send_generate_invoice')) {
                $table->enum('send_generate_invoice', ['Não', 'Sim'])->nullable()->after('day_generate_invoice');
            }
            if (!Schema::hasColumn('users', 'access_token_inter')) {
                $table->string('access_token_inter')->nullable()->after('send_generate_invoice');
            }
            if (!Schema::hasColumn('users', 'access_token_paghiper')) {
                $table->string('access_token_paghiper')->nullable()->after('access_token_inter');
            }
            if (!Schema::hasColumn('users', 'use_intermedium')) {
                $table->char('use_intermedium', 1)->default('n')->nullable()->after('access_token_paghiper');
            }
            if (!Schema::hasColumn('users', 'api_status_whatsapp')) {
                $table->string('api_status_whatsapp')->nullable()->after('use_intermedium');
            }
            if (!Schema::hasColumn('users', 'typebot_id')) {
                $table->string('typebot_id')->nullable()->after('api_status_whatsapp');
            }
            if (!Schema::hasColumn('users', 'typebot_enable')) {
                $table->char('typebot_enable', 1)->default('n')->nullable()->after('typebot_id');
            }
            if (!Schema::hasColumn('users', 'environment_asaas')) {
                $table->enum('environment_asaas', ['Teste', 'Produção'])->default('Teste')->nullable()->after('typebot_enable');
            }
            if (!Schema::hasColumn('users', 'at_asaas_prod')) {
                $table->string('at_asaas_prod')->nullable()->after('environment_asaas');
            }
            if (!Schema::hasColumn('users', 'at_asaas_test')) {
                $table->string('at_asaas_test')->nullable()->after('at_asaas_prod');
            }
            if (!Schema::hasColumn('users', 'asaas_url_test')) {
                $table->string('asaas_url_test')->default('https://sandbox.asaas.com/api/')->nullable()->after('at_asaas_test');
            }
            if (!Schema::hasColumn('users', 'asaas_url_prod')) {
                $table->string('asaas_url_prod')->default('https://api.asaas.com/')->nullable()->after('asaas_url_test');
            }
            if (!Schema::hasColumn('users', 'chave_pix')) {
                $table->text('chave_pix')->nullable()->after('asaas_url_prod');
            }
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
            $columns = [
                'api_session_whatsapp', 'api_token_whatsapp', 'inter_chave_pix',
                'day_generate_invoice', 'send_generate_invoice', 'access_token_inter',
                'access_token_paghiper', 'use_intermedium', 'api_status_whatsapp',
                'typebot_id', 'typebot_enable', 'environment_asaas', 'at_asaas_prod',
                'at_asaas_test', 'asaas_url_test', 'asaas_url_prod', 'chave_pix'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
