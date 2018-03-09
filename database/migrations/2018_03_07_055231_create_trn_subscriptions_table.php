<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_subscriptions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('member_id')->index('FK_trn_subscriptions_mst_members_1')->comment('links to unique record id of mst_members');
            $table->integer('invoice_id')->index('FK_trn_subscriptions_trn_invoice')->comment('links to unique record id of trn_invoice');
            $table->integer('plan_id')->index('FK_trn_subscriptions_mst_plans_2')->comment('links to unique record if of mst_plans');
            $table->date('start_date')->comment('start date of subscription');
            $table->date('end_date')->comment('end date of subscription');
            $table->boolean('status')->comment('0 = expired, 1 = ongoing, 2 = renewed, 3 = canceled');
            $table->boolean('is_renewal')->comment('0= false , 1=true');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_trn_subscriptions_mst_staff_3');
            $table->integer('updated_by')->unsigned()->index('FK_trn_subscriptions_mst_staff_4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trn_subscriptions');
    }
}
