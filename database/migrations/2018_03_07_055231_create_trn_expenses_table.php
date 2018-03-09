<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_expenses', function (Blueprint $table) {
            $table->integer('id', true)->comment('Unique Record Id for system');
            $table->string('name', 50)->comment('name of the expense');
            $table->integer('category_id')->index('FK_trn_expenses_mst_expenses_categories_1')->comment('name of the category of expense');
            $table->integer('amount')->comment('expense amount');
            $table->date('due_date')->comment('Due Date for the expense created');
            $table->boolean('repeat')->comment('0 = never repeat , 1 = every day , 2 = every week , 3 = every month , 4 = every year');
            $table->boolean('paid')->comment('0 = false , 1 = true i.e. paid');
            $table->string('note', 50);
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_trn_expenses_mst_users_2');
            $table->integer('updated_by')->unsigned()->nullable()->index('FK_trn_expenses_mst_users_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trn_expenses');
    }
}
