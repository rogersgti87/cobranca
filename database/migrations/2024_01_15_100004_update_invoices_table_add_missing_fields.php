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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'customer_id')) {
                $table->integer('customer_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('invoices', 'gateway_payment')) {
                $table->string('gateway_payment')->nullable()->after('status');
            }
            if (!Schema::hasColumn('invoices', 'tax')) {
                $table->enum('tax', ['Sim', 'Não'])->nullable()->after('billet_digitable');
            }
            if (!Schema::hasColumn('invoices', 'tax_percent')) {
                $table->integer('tax_percent')->default(1)->nullable()->after('tax');
            }
            if (!Schema::hasColumn('invoices', 'notificated')) {
                $table->char('notificated', 1)->default('n')->nullable()->after('tax_percent');
            }
            if (!Schema::hasColumn('invoices', 'msg_erro')) {
                $table->text('msg_erro')->nullable()->after('notificated');
            }

            // Atualizar enum status
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Pendente','Processamento','Pago','Cancelado','Erro','Gerando','Estabelecimento') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columns = ['tax', 'tax_percent', 'notificated', 'msg_erro'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
