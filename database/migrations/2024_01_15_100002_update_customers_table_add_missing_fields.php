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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'gateway_pix')) {
                $table->string('gateway_pix')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('customers', 'gateway_billet')) {
                $table->string('gateway_billet')->nullable()->after('gateway_pix');
            }
            if (!Schema::hasColumn('customers', 'gateway_card')) {
                $table->string('gateway_card')->nullable()->after('gateway_billet');
            }
            if (!Schema::hasColumn('customers', 'obs')) {
                $table->text('obs')->nullable()->after('gateway_card');
            }
            if (!Schema::hasColumn('customers', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('obs');
            }
            if (!Schema::hasColumn('customers', 'notification_email')) {
                $table->char('notification_email', 1)->default('s')->nullable()->after('birthdate');
            }
            if (!Schema::hasColumn('customers', 'notificate_5_days')) {
                $table->char('notificate_5_days', 1)->default('s')->nullable()->after('notification_email');
            }
            if (!Schema::hasColumn('customers', 'notificate_2_days')) {
                $table->char('notificate_2_days', 1)->default('s')->nullable()->after('notificate_5_days');
            }
            if (!Schema::hasColumn('customers', 'notificate_due')) {
                $table->char('notificate_due', 1)->default('s')->nullable()->after('notificate_2_days');
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
        Schema::table('customers', function (Blueprint $table) {
            $columns = [
                'gateway_pix', 'gateway_billet', 'gateway_card', 'obs',
                'birthdate', 'notification_email', 'notificate_5_days',
                'notificate_2_days', 'notificate_due'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
