<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_invoice', function (Blueprint $table) {
            $table->integer('id', true)->comment('Unique Record Id for system');
            $table->integer('member_id')->index('FK_trn_invoice_mst_members_1')->comment('links to unique record id of mst_members');
            $table->integer('total')->comment('total fees/amount generated');
            $table->integer('pending_amount')->comment('pending amount');
            $table->text('note', 65535)->comment('note regarding payments');
            $table->boolean('status')->comment('0 = Unpaid, 1 = Paid,  2 = Partial 3 = overpaid');
            $table->string('invoice_number', 50)->comment('number of the inovice/reciept');
            $table->string('discount_percent', 50)->nullable();
            $table->string('discount_amount', 50)->nullable();
            $table->string('discount_note', 50)->nullable();
            $table->timestamps();
            
            $table->integer('tax');
            $table->integer('additional_fees')->nullable();

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
        Schema::drop('trn_invoice');
    }
}
