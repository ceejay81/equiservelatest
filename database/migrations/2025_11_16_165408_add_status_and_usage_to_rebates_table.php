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
            $table->enum('status', ['available', 'used'])->default('available')->after('rebate_amount');
            $table->enum('used_for', ['purchase', 'loan_payment'])->nullable()->after('status');
            $table->foreignId('applied_to_loan_id')->nullable()->constrained('loans')->nullOnDelete()->after('used_for');
            $table->timestamp('used_at')->nullable()->after('applied_to_loan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rebates', function (Blueprint $table) {
            $table->dropForeign(['applied_to_loan_id']);
            $table->dropColumn(['status', 'used_for', 'applied_to_loan_id', 'used_at']);
        });
    }
};
