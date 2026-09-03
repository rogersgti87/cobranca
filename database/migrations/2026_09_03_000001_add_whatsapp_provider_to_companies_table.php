<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'whatsapp_provider')) {
                $table->string('whatsapp_provider', 20)
                    ->default('evogo')
                    ->after('api_status_whatsapp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'whatsapp_provider')) {
                $table->dropColumn('whatsapp_provider');
            }
        });
    }
};
