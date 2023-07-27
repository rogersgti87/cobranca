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
        Schema::create('email_events', function (Blueprint $table) {
            $table->id('id');
            $table->integer('user_id');
            $table->string('event')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->string('message_id')->nullable();
            $table->string('recipient')->nullable();
            $table->string('sender')->nullable();
            $table->string('subject')->nullable();
            $table->timestamps();
        });
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
