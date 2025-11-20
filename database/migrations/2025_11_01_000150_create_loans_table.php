<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('sale_id')
                  ->constrained('sales')
                  ->cascadeOnDelete();

            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->cascadeOnDelete();

            // Payment / term fields
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('down_payment', 12, 2)->default(0);
            $table->integer('term_months');
            $table->decimal('interest_rate', 5, 2)->default(0.00);
            $table->decimal('monthly_amount', 12, 2);
            $table->decimal('balance', 12, 2);

            // Dates
            $table->date('start_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->date('end_date')->nullable();

            // Status and remarks
            $table->enum('status', ['active', 'completed', 'overdue', 'cancelled'])->default('active');
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
