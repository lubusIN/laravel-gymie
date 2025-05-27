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
            $table->string('email')->unique();
            $table->string('contact')->nullable();
            $table->enum('gender', ['male' , 'female', 'others'])->nullable();
            $table->date('dob')->nullable();
            $table->string('occupation')->nullable();
            $table->enum('status', ['lead', 'member', 'lost'])->default('lead')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->integer('pincode')->nullable();
            $table->string('interested_in')->nullable();
            $table->string('source')->nullable();
            $table->string('why_do_you_plan_to_join')->nullable();
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
