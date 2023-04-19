<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTrnChequeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_cheque_details', function (Blueprint $table) {
            $table->foreign('created_by', 'FK_trn_cheque_details_mst_users')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'FK_trn_cheque_details_mst_users_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('payment_id', 'FK_trn_cheque_details_trn_payment_details')->references('id')->on('trn_payment_details')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_cheque_details', function (Blueprint $table) {
            $table->dropForeign('FK_trn_cheque_details_mst_users');
            $table->dropForeign('FK_trn_cheque_details_mst_users_2');
            $table->dropForeign('FK_trn_cheque_details_trn_payment_details');
        });
    }
}
