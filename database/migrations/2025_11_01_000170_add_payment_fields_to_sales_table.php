<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_mode', ['cash','online'])->nullable()->after('sale_type');
            $table->decimal('amount_tendered', 12, 2)->nullable()->after('payment_mode');
            $table->decimal('amount_paid', 12, 2)->nullable()->after('amount_tendered');
            $table->string('reference_number')->nullable()->after('amount_paid');
            $table->string('proof_image_path')->nullable()->after('reference_number');
            $table->decimal('discount_total', 12, 2)->nullable()->after('total_amount');
            $table->string('discount_reason')->nullable()->after('discount_total');
            $table->index(['payment_mode', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['payment_mode', 'created_at']);
            $table->dropColumn([
                'payment_mode',
                'amount_tendered',
                'amount_paid',
                'reference_number',
                'proof_image_path',
                'discount_total',
                'discount_reason',
            ]);
        });
    }
};
