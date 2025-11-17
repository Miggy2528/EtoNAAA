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
            $table->foreignId('meat_cut_id')
                ->nullable()
                ->after('id')
                ->constrained('meat_cuts')
                ->nullOnDelete();
            
            $table->decimal('weight_per_unit', 8, 2)
                ->nullable()
                ->after('quantity')
                ->comment('Weight in kilograms');
            
            $table->date('expiration_date')
                ->nullable()
                ->after('weight_per_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['meat_cut_id']);
            $table->dropColumn('meat_cut_id');
            $table->dropColumn('weight_per_unit');
            $table->dropColumn('expiration_date');
        });
    }
}; 