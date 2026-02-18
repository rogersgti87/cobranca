<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Esta migration marca as migrations antigas como já executadas
     * para que o Laravel não tente criar tabelas que já existem.
     *
     * @return void
     */
    public function up()
    {
        // Migrations antigas que devem ser marcadas como executadas
        $oldMigrations = [
            '2014_10_12_000000_create_users_table',
            '2014_10_12_100000_create_password_resets_table',
            '2019_08_19_000000_create_failed_jobs_table',
            '2019_12_14_000001_create_personal_access_tokens_table',
            '2019_12_14_000002_customers_table',
            '2020_07_20_071395_create_services_table',
            '2020_07_20_100412_create_customer_services_table',
            '2020_07_20_111455_create_invoices_table',
            '2020_07_20_111456_create_invoice_notifications_table',
            '2020_07_20_111457_create_email_events_table',
            '2020_07_20_111458_create_gateway_payments_table',
            '2023_09_14_155141_create_jobs_table',
            '2023_09_14_155142_create_payables_table',
        ];

        foreach ($oldMigrations as $migration) {
            // Verificar se já não está registrada
            $exists = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();
            
            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => 1
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Não fazer nada no down
    }
};
