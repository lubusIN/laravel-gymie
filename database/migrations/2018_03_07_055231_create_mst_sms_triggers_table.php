<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstSmsTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_sms_triggers', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 50);
            $table->string('alias', 50);
            $table->string('message', 500);
            $table->boolean('status');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_sms_triggers');
    }
}
