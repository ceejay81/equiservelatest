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
        Schema::table('products', function (Blueprint $table) {
            $table->string('model')->nullable()->after('brand');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('stock');
            $table->text('description')->nullable()->after('status');
            $table->string('image_path')->nullable()->after('description');
            $table->integer('reorder_level')->default(5)->after('image_path');
            $table->string('supplier')->nullable()->after('reorder_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['model', 'status', 'description', 'image_path', 'reorder_level', 'supplier']);
        });
    }
};
