<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_invoice_details', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('invoice_id')->index('FK_trn_invoice_details_trn_invoice_1')->comment('links to unique record id of trn_invoice');
            $table->integer('item_amount')->comment('amount of the items');
            $table->timestamps();
            $table->integer('plan_id')->default(1)->index('trn_invoice_details_plan_id_foreign');

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
        Schema::drop('trn_invoice_details');
    }
}
