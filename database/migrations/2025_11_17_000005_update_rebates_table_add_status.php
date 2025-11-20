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
        Schema::table('rebates', function (Blueprint $table) {
            // Modify existing status column to add new statuses
            $table->enum('status', ['available', 'used', 'forfeited', 'expired'])->default('available')->change();
            
            // Add new columns
            $table->foreignId('applied_to_payment_id')->nullable()->after('applied_to_loan_id')->constrained('payments')->onDelete('set null')->comment('Payment where rebate was applied');
            $table->date('applied_date')->nullable()->after('applied_to_payment_id')->comment('Date rebate was applied');
            $table->date('expiry_date')->nullable()->after('applied_date')->comment('Rebate expiration date');
            
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rebates', function (Blueprint $table) {
            // Revert status column to original values
            $table->enum('status', ['available', 'used'])->default('available')->change();
            
            // Drop new columns
            $table->dropForeign(['applied_to_payment_id']);
            $table->dropColumn([
                'applied_to_payment_id',
                'applied_date',
                'expiry_date'
            ]);
        });
    }
};
