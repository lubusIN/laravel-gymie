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
            $table->boolean('paid')->comment('0 = false , 1 = true i.e. paid')->default(false);
            $table->string('note', 50)->nullable();
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
        Schema::drop('trn_expenses');
    }
}
