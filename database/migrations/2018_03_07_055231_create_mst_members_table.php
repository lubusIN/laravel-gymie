<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_members', function (Blueprint $table) {
            $table->integer('id', true)->comment('Unique Record Id for system');
            $table->string('member_code', 50)->unique('member_id')->comment('Unique member id for reference');
            $table->string('name', 50)->comment('member\'s name');
            $table->string('photo', 50)->comment('member\'s photo');
            $table->date('DOB')->comment('member\'s date of birth');
            $table->string('email', 50)->unique('email')->comment('member\'s email id');
            $table->string('address', 200)->comment('member\'s address');
            $table->boolean('status')->comment('0 for inactive , 1 for active');
            $table->string('proof_name', 50)->comment('name of the proof provided by member');
            $table->string('proof_photo', 50)->comment('photo of the proof');
            $table->char('gender', 50)->comment('member\'s gender');
            $table->string('contact', 11)->unique('contact')->comment('member\'s contact number');
            $table->string('emergency_contact', 11);
            $table->string('health_issues', 50);
            $table->integer('pin_code');
            $table->string('occupation', 50);
            $table->string('aim', 50);
            $table->string('source', 50);
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_mst_members_mst_users_1');
            $table->integer('updated_by')->unsigned()->index('FK_mst_members_mst_users_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_members');
    }
}
