<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('invoice_notifications')) {
            Schema::create('invoice_notifications', function (Blueprint $table) {
                $table->id('id');
                $table->integer('user_id');
                $table->integer('invoice_id');
                $table->string('type_send');
                $table->string('subject')->nullable();
                $table->datetime('date');
                $table->string('email_id')->nullable();
                $table->string('status')->nullable();
                $table->text('message_status')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_notifications');
    }
}
