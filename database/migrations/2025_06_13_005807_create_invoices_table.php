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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->float('discount')->nullable();
            $table->float('tax')->nullable();
            $table->float('discount_amount')->nullable();
            $table->string('discount_note')->nullable();
            $table->float('paid_amount')->default(0);
            $table->float('total_amount')->default(0);
            $table->float('due_amount')->default(0);
            $table->float('subscription_fee')->default(0);
            $table->enum('status', ['issued', 'paid', 'partial', 'overdue', 'refund', 'cancelled'])->default('issued');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
