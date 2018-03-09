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
            $table->string('outcome', 50);
            $table->boolean('status')->comment('0 = Pending , 1 = Done');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_trn_enquiry_followups_mst_staff_2');
            $table->integer('updated_by')->unsigned()->index('FK_trn_enquiry_followups_mst_staff_3');
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
