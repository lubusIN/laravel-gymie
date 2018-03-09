<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToMstPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_plans', function (Blueprint $table) {
            $table->foreign('service_id', 'FK_mst_plans_mst_services')->references('id')->on('mst_services')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_by', 'FK_mst_plans_mst_users_1')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'FK_mst_plans_mst_users_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_plans', function (Blueprint $table) {
            $table->dropForeign('FK_mst_plans_mst_services');
            $table->dropForeign('FK_mst_plans_mst_users_1');
            $table->dropForeign('FK_mst_plans_mst_users_2');
        });
    }
}
