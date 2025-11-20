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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // payment_due, overdue, low_stock, etc.
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('related_type')->nullable(); // loan, product, customer
            $table->unsignedBigInteger('related_id')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_actioned')->default(false); // marked as contacted/resolved
            $table->foreignId('actioned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('actioned_at')->nullable();
            $table->json('data')->nullable(); // additional data (phone, amount, etc.)
            $table->timestamps();
            
            $table->index(['type', 'is_read', 'priority']);
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
