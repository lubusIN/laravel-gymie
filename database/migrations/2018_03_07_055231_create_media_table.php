<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('model_id')->unsigned();
            $table->string('model_type');
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('disk');
            $table->integer('size')->unsigned();
            $table->text('manipulations', 65535);
            $table->text('custom_properties', 65535);
            $table->integer('order_column')->unsigned()->nullable();
            $table->timestamps();
            $table->index(['model_id', 'model_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('media');
    }
}
