<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTrnAccessLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_access_log', function (Blueprint $table) {
            $table->foreign('user_id', 'FK_trn_activities_mst_users_1')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_access_log', function (Blueprint $table) {
            $table->dropForeign('FK_trn_activities_mst_users_1');
        });
    }
}
