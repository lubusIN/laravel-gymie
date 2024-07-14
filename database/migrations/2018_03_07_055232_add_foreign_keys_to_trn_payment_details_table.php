<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTrnPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_payment_details', function (Blueprint $table) {
            $table->foreign('invoice_id', 'FK_trn_payment_details_1')->references('id')->on('trn_invoice')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_by', 'FK_trn_payment_details_mst_staff_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'FK_trn_payment_details_mst_staff_3')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_payment_details', function (Blueprint $table) {
            $table->dropForeign('FK_trn_payment_details_1');
            $table->dropForeign('FK_trn_payment_details_mst_staff_2');
            $table->dropForeign('FK_trn_payment_details_mst_staff_3');
        });
    }
}
