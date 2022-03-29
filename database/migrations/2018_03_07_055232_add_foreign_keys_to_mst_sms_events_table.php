<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMstSmsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_sms_events', function (Blueprint $table) {
            $table->foreign('created_by', 'FK_mst_sms_events_mst_users_1')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'FK_mst_sms_events_mst_users_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_sms_events', function (Blueprint $table) {
            $table->dropForeign('FK_mst_sms_events_mst_users_1');
            $table->dropForeign('FK_mst_sms_events_mst_users_2');
        });
    }
}
