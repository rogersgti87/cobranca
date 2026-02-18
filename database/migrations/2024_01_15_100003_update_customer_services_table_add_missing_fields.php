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
        Schema::table('customer_services', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_services', 'gateway_payment')) {
                $table->string('gateway_payment')->nullable()->after('period');
            }
            if (!Schema::hasColumn('customer_services', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('gateway_payment');
            }
            if (!Schema::hasColumn('customer_services', 'start_billing')) {
                $table->date('start_billing')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('customer_services', 'end_billing')) {
                $table->date('end_billing')->nullable()->after('start_billing');
            }
            if (!Schema::hasColumn('customer_services', 'tax')) {
                $table->enum('tax', ['Sim', 'Não'])->nullable()->after('end_billing');
            }
            if (!Schema::hasColumn('customer_services', 'tax_percent')) {
                $table->integer('tax_percent')->default(1)->nullable()->after('tax');
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
        Schema::table('customer_services', function (Blueprint $table) {
            $columns = [
                'gateway_payment', 'payment_method', 'start_billing',
                'end_billing', 'tax', 'tax_percent'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('customer_services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
