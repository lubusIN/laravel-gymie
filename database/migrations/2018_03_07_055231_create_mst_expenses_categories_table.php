<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstExpensesCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_expenses_categories', function (Blueprint $table) {
            $table->integer('id', true)->comment('Unique Record Id for system');
            $table->string('name', 50)->comment('category name');
            $table->boolean('status');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_mst_expenses_categories_mst_users_1');
            $table->integer('updated_by')->unsigned()->index('FK_mst_expenses_categories_mst_users_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_expenses_categories');
    }
}
