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
            $table->string('id_type')->nullable()->after('remarks')->comment('Type of ID (e.g., Drivers License, Passport, National ID)');
            $table->string('id_number')->nullable()->after('id_type')->comment('ID number from the document');
            $table->string('id_image_path')->nullable()->after('id_number')->comment('Path to uploaded ID image');
            $table->timestamp('id_verified_at')->nullable()->after('id_image_path')->comment('When ID was submitted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['id_type', 'id_number', 'id_image_path', 'id_verified_at']);
        });
    }
};
