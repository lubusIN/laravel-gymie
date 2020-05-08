<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnEnquiryFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_enquiry_followups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('enquiry_id')->unsigned()->index('FK_trn_enquiry_followups_mst_enquiries_1');
            $table->string('followup_by', 50);
            $table->date('due_date');
            $table->string('outcome', 50)->nullable();
            $table->boolean('status')->comment('0 = Pending , 1 = Done')->default(false);
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
        Schema::drop('trn_enquiry_followups');
    }
}
