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
        Schema::table('payables', function (Blueprint $table) {
            // Remover coluna date_invoice se existir (não existe no banco real)
            if (Schema::hasColumn('payables', 'date_invoice')) {
                $table->dropColumn('date_invoice');
            }

            // Adicionar colunas que faltam
            if (!Schema::hasColumn('payables', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->after('user_id')->index();
            }
            if (!Schema::hasColumn('payables', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('supplier_id')->index();
            }
            if (!Schema::hasColumn('payables', 'description')) {
                $table->text('description')->after('category_id');
            }
            if (!Schema::hasColumn('payables', 'type')) {
                $table->enum('type', ['Fixa', 'Recorrente', 'Parcelada'])->default('Fixa')->after('price')->index();
            }
            if (!Schema::hasColumn('payables', 'recurrence_period')) {
                $table->string('recurrence_period', 20)->nullable()->after('status');
            }
            if (!Schema::hasColumn('payables', 'recurrence_day')) {
                $table->integer('recurrence_day')->nullable()->after('recurrence_period');
            }
            if (!Schema::hasColumn('payables', 'recurrence_end')) {
                $table->date('recurrence_end')->nullable()->after('recurrence_day');
            }
            if (!Schema::hasColumn('payables', 'installments')) {
                $table->integer('installments')->nullable()->after('recurrence_end');
            }
            if (!Schema::hasColumn('payables', 'installment_number')) {
                $table->integer('installment_number')->nullable()->after('installments');
            }
            if (!Schema::hasColumn('payables', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('installment_number')->index();
            }

            // Adicionar índices
            $table->index('user_id');
            $table->index('date_due');
            $table->index('status');

            // Alterar description para NOT NULL (se ainda não foi)
            DB::statement("ALTER TABLE payables MODIFY COLUMN description TEXT NOT NULL");
            
            // Alterar payment_method para nullable
            DB::statement("ALTER TABLE payables MODIFY COLUMN payment_method VARCHAR(50) NULL");
            
            // Alterar status para ter default
            DB::statement("ALTER TABLE payables MODIFY COLUMN status ENUM('Pendente','Pago','Cancelado') DEFAULT 'Pendente' NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payables', function (Blueprint $table) {
            $columns = [
                'supplier_id', 'category_id', 'type', 'recurrence_period',
                'recurrence_day', 'recurrence_end', 'installments',
                'installment_number', 'parent_id'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('payables', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
