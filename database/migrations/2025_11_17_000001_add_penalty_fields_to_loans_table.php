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
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('late_penalty_rate', 5, 2)->default(3.00)->after('status')->comment('Percentage rate for late payment penalty');
            $table->decimal('maturity_penalty_rate', 5, 2)->default(5.00)->after('late_penalty_rate')->comment('Percentage rate for maturity penalty');
            $table->integer('grace_period_days')->default(3)->after('maturity_penalty_rate')->comment('Days after due date before penalty applies');
            $table->decimal('accumulated_penalties', 10, 2)->default(0)->after('grace_period_days')->comment('Total penalties charged on this loan');
            $table->date('maturity_date')->nullable()->after('accumulated_penalties')->comment('Final due date for loan completion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'late_penalty_rate',
                'maturity_penalty_rate',
                'grace_period_days',
                'accumulated_penalties',
                'maturity_date'
            ]);
        });
    }
};
