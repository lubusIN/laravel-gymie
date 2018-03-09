<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstSmsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_sms_events', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 50);
            $table->string('message', 500);
            $table->string('description', 140);
            $table->dateTime('date');
            $table->boolean('status');
            $table->integer('send_to')->comment('0 = active members , 1 = inactive members , 2= lead enquiries , 3 = lost enquiries');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_mst_sms_events_mst_users_1');
            $table->integer('updated_by')->unsigned()->index('FK_mst_sms_events_mst_users_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_sms_events');
    }
}
