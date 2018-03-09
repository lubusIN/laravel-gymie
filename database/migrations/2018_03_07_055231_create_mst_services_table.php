<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_services', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 50);
            $table->string('description', 50);
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_mst_services_mst_users_1');
            $table->integer('updated_by')->unsigned()->index('FK_mst_services_mst_users_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_services');
    }
}
