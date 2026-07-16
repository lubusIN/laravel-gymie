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
        Schema::table('users', function (Blueprint $table) {
            $table->string('pincode')->nullable()->change();
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('pincode')->nullable()->change();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('pincode')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('pincode')->nullable()->change();
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->text('pincode')->nullable()->change();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->text('pincode')->nullable()->change();
        });
    }
};
