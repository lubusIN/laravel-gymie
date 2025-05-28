<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('contact')->nullable();
            $table->date('date')->nullable()->default(now());
            $table->enum('gender', ['male' , 'female', 'other'])->default('male')->nullable();
            $table->date('dob')->nullable();
            $table->enum('occupation', ['student','housewife', 'self_employed', 'professional', 'freelancer', 'others'])->default('student')->nullable();
            $table->enum('status', ['lead', 'member', 'lost'])->default('lead')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('pincode')->nullable();
            $table->string('interested_in')->nullable();
            $table->enum('source', ['promotions', 'word_of_mouth', 'others'])->default('promotions')->nullable();
            $table->enum('why_do_you_plan_to_join', ['fitness', 'body_building', 'fatloss', 'weightgain', 'others'])->default('fitness')->nullable();
            $table->date('start_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
