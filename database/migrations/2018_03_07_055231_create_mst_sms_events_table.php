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
            
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
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
