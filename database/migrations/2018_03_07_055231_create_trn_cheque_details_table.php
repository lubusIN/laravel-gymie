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
            $table->integer('created_by')->unsigned()->index('FK_trn_cheque_details_mst_users');
            $table->integer('updated_by')->unsigned()->index('FK_trn_cheque_details_mst_users_2');
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
