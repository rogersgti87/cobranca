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
        if (!Schema::hasTable('payable_reversals')) {
            Schema::create('payable_reversals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payable_id')->index();
                $table->integer('user_id')->index();
                $table->timestamp('reversed_at')->useCurrent();
                $table->text('reversal_reason')->nullable();
                $table->timestamps();

                $table->foreign('payable_id')->references('id')->on('payables')->onDelete('cascade');
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
        Schema::dropIfExists('payable_reversals');
    }
};
