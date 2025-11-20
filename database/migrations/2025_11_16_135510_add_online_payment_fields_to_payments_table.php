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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->after('mode_of_payment');
            $table->string('proof_image_path')->nullable()->after('reference_number');
            $table->string('payment_bank')->nullable()->after('proof_image_path');
            $table->timestamp('payment_timestamp')->nullable()->after('payment_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['reference_number', 'proof_image_path', 'payment_bank', 'payment_timestamp']);
        });
    }
};
