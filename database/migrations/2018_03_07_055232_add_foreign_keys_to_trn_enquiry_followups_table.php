<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTrnEnquiryFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_enquiry_followups', function (Blueprint $table) {
            $table->foreign('enquiry_id', 'FK_trn_enquiry_followups_mst_enquiries_1')->references('id')->on('mst_enquiries')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_by', 'FK_trn_enquiry_followups_mst_staff_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'FK_trn_enquiry_followups_mst_staff_3')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_enquiry_followups', function (Blueprint $table) {
            $table->dropForeign('FK_trn_enquiry_followups_mst_enquiries_1');
            $table->dropForeign('FK_trn_enquiry_followups_mst_staff_2');
            $table->dropForeign('FK_trn_enquiry_followups_mst_staff_3');
        });
    }
}
