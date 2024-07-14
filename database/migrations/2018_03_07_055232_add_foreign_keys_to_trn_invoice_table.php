<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTrnInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_invoice', function (Blueprint $table) {
            $table->foreign('member_id', 'FK_trn_invoice_mst_members_1')->references('id')->on('mst_members')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_by', 'FK_trn_invoice_mst_staff_1')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'FK_trn_invoice_mst_staff_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_invoice', function (Blueprint $table) {
            $table->dropForeign('FK_trn_invoice_mst_members_1');
            $table->dropForeign('FK_trn_invoice_mst_staff_1');
            $table->dropForeign('FK_trn_invoice_mst_staff_2');
        });
    }
}
