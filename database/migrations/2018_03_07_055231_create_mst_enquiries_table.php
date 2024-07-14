<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_enquiries', function (Blueprint $table) {
            $table->increments('id')->comment('Unique record ID');
            $table->string('name', 50);
            $table->date('DOB');
            $table->string('email', 50);
            $table->string('address', 200);
            $table->boolean('status')->comment('0 = Lost , 1 = Lead  , 2 =Member');
            $table->string('contact', 50);
            $table->string('gender', 50);
            $table->integer('pin_code');
            $table->string('occupation', 50);
            $table->date('start_by');
            $table->string('interested_in', 50);
            $table->string('aim', 50);
            $table->string('source', 50);
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_mst_enquiries_mst_staff_1');
            $table->integer('updated_by')->unsigned()->index('FK_mst_enquiries_mst_staff_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_enquiries');
    }
}
