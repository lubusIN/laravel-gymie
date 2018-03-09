<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnSmsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_sms_log', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('number', 50);
            $table->string('message', 500);
            $table->string('shoot_id', 200);
            $table->string('status', 200)->default('NA');
            $table->dateTime('send_time');
            $table->string('sender_id', 11);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trn_sms_log');
    }
}
