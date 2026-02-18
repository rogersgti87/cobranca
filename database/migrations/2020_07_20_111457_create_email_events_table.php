<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('email_events')) {
            Schema::create('email_events', function (Blueprint $table) {
                $table->id('id');
                $table->integer('user_id');
                $table->string('event')->nullable();
                $table->string('email')->nullable();
                $table->string('identification')->nullable();
                $table->datetime('date')->nullable();
                $table->string('message_id')->nullable();
                $table->string('subject')->nullable();
                $table->string('tag')->nullable();
                $table->string('sending_ip')->nullable();
                $table->string('ts_epoch')->nullable();
                $table->string('link')->nullable();
                $table->string('reason')->nullable();
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
        Schema::dropIfExists('email_events');
    }
}
