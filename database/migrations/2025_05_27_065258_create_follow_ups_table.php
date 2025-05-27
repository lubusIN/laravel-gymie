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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->onDelete('cascade');
            $table->date('follow_up_date')->nullable();
            $table->enum('follow_up_method', ['call', 'email', 'in_person', 'whatsapp', 'other'])->default('call')->nullable();
            $table->string('outcome')->nullable();
            $table->enum('status', ['pending', 'done'])->default('pending')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
