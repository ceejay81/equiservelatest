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
        Schema::create('loan_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null')->comment('Payment this penalty was charged with');
            $table->enum('type', ['late_payment', 'maturity'])->comment('Type of penalty');
            $table->decimal('rate', 5, 2)->comment('Penalty rate percentage used');
            $table->decimal('base_amount', 10, 2)->comment('Amount penalty was calculated on');
            $table->decimal('penalty_amount', 10, 2)->comment('Calculated penalty amount');
            $table->date('due_date')->comment('Original due date');
            $table->date('charged_date')->comment('Date penalty was charged');
            $table->integer('days_late')->nullable()->comment('Number of days late');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['loan_id', 'type']);
            $table->index('charged_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_penalties');
    }
};
