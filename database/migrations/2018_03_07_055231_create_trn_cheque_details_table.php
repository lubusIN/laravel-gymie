<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnChequeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_cheque_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('payment_id')->index('FK_trn_cheque_details_trn_payment_details');
            $table->string('number', 50);
            $table->date('date');
            $table->boolean('status')->comment('0 = recieved , 1 = deposited , 2 = cleared , 3 = bounced');
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
        Schema::drop('trn_cheque_details');
    }
}
