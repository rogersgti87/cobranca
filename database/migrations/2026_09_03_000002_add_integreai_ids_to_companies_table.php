<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'integreai_company_id')) {
                $table->unsignedBigInteger('integreai_company_id')->nullable()->after('whatsapp_provider');
            }

            if (! Schema::hasColumn('companies', 'integreai_instance_id')) {
                $table->unsignedBigInteger('integreai_instance_id')->nullable()->after('integreai_company_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'integreai_instance_id')) {
                $table->dropColumn('integreai_instance_id');
            }

            if (Schema::hasColumn('companies', 'integreai_company_id')) {
                $table->dropColumn('integreai_company_id');
            }
        });
    }
};
