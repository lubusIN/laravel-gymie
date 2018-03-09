<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToMstExpensesCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_expenses_categories', function (Blueprint $table) {
            $table->foreign('created_by', 'FK_mst_expenses_categories_mst_users_1')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('NO ACTION');
            $table->foreign('updated_by', 'FK_mst_expenses_categories_mst_users_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_expenses_categories', function (Blueprint $table) {
            $table->dropForeign('FK_mst_expenses_categories_mst_users_1');
            $table->dropForeign('FK_mst_expenses_categories_mst_users_2');
        });
    }
}
