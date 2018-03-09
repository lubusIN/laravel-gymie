<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToTrnExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trn_expenses', function (Blueprint $table) {
            $table->foreign('category_id', 'FK_trn_expenses_mst_expenses_categories_1')->references('id')->on('mst_expenses_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_by', 'FK_trn_expenses_mst_users_2')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'FK_trn_expenses_mst_users_3')->references('id')->on('mst_users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_expenses', function (Blueprint $table) {
            $table->dropForeign('FK_trn_expenses_mst_expenses_categories_1');
            $table->dropForeign('FK_trn_expenses_mst_users_2');
            $table->dropForeign('FK_trn_expenses_mst_users_3');
        });
    }
}
