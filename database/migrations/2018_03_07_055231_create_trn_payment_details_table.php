<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_payment_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('invoice_id')->index('FK_trn_payment_details_1')->comment('links to unique record id of trn_invoice');
            $table->integer('payment_amount')->comment('amount of transaction being done');
            $table->string('mode', 50)->comment('1 = Cash , 0 = Cheque');
            $table->string('note', 50)->comment('misc. note');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_trn_payment_details_mst_staff_2');
            $table->integer('updated_by')->unsigned()->index('FK_trn_payment_details_mst_staff_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trn_payment_details');
    }
}
