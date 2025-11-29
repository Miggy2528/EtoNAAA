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
        // Add void columns to utility_expenses
        if (Schema::hasTable('utility_expenses')) {
            Schema::table('utility_expenses', function (Blueprint $table) {
                $table->boolean('is_void')->default(false)->after('created_by');
                $table->text('void_reason')->nullable()->after('is_void');
                $table->timestamp('voided_at')->nullable()->after('void_reason');
                $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete()->after('voided_at');
                
                $table->index('is_void');
            });
        }

        // Add void columns to payroll_records
        if (Schema::hasTable('payroll_records')) {
            Schema::table('payroll_records', function (Blueprint $table) {
                $table->boolean('is_void')->default(false)->after('created_by');
                $table->text('void_reason')->nullable()->after('is_void');
                $table->timestamp('voided_at')->nullable()->after('void_reason');
                $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete()->after('voided_at');
                
                $table->index('is_void');
            });
        }

        // Add void columns to other_expenses
        if (Schema::hasTable('other_expenses')) {
            Schema::table('other_expenses', function (Blueprint $table) {
                $table->boolean('is_void')->default(false)->after('created_by');
                $table->text('void_reason')->nullable()->after('is_void');
                $table->timestamp('voided_at')->nullable()->after('void_reason');
                $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete()->after('voided_at');
                
                $table->index('is_void');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('utility_expenses')) {
            Schema::table('utility_expenses', function (Blueprint $table) {
                $table->dropColumn(['is_void', 'void_reason', 'voided_at', 'voided_by']);
            });
        }

        if (Schema::hasTable('payroll_records')) {
            Schema::table('payroll_records', function (Blueprint $table) {
                $table->dropColumn(['is_void', 'void_reason', 'voided_at', 'voided_by']);
            });
        }

        if (Schema::hasTable('other_expenses')) {
            Schema::table('other_expenses', function (Blueprint $table) {
                $table->dropColumn(['is_void', 'void_reason', 'voided_at', 'voided_by']);
            });
        }
    }
};
