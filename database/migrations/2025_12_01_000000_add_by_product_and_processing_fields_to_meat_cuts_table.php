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
        Schema::table('meat_cuts', function (Blueprint $table) {
            $table->boolean('is_by_product')->default(false)->after('is_available');
            $table->boolean('is_processing_meat')->default(false)->after('is_by_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meat_cuts', function (Blueprint $table) {
            $table->dropColumn(['is_by_product', 'is_processing_meat']);
        });
    }
};