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
            $table->integer('created_by')->unsigned()->index('FK_trn_invoice_details_mst_staff_2');
            $table->integer('updated_by')->unsigned()->index('FK_trn_invoice_details_mst_staff_3');
            $table->integer('plan_id')->default(1)->index('trn_invoice_details_plan_id_foreign');
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
