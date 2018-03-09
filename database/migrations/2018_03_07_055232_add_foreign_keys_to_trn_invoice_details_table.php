<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTrnInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_invoice_details', function (Blueprint $table) {
            $table->foreign('created_by', 'FK_trn_invoice_details_mst_staff_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'FK_trn_invoice_details_mst_staff_3')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('invoice_id', 'FK_trn_invoice_details_trn_invoice_1')->references('id')->on('trn_invoice')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('plan_id')->references('id')->on('mst_plans')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_invoice_details', function (Blueprint $table) {
            $table->dropForeign('FK_trn_invoice_details_mst_staff_2');
            $table->dropForeign('FK_trn_invoice_details_mst_staff_3');
            $table->dropForeign('FK_trn_invoice_details_trn_invoice_1');
            $table->dropForeign('trn_invoice_details_plan_id_foreign');
        });
    }
}
