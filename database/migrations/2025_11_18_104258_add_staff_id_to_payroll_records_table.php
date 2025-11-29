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
        Schema::table('payroll_records', function (Blueprint $table) {
            // Add staff_id column after user_id
            $table->foreignId('staff_id')->nullable()->after('user_id')->constrained('staff')->cascadeOnDelete();
            
            // Add index for staff_id
            $table->index('staff_id');
            
            // Add working_days and daily_rate for more detailed payroll tracking
            $table->integer('working_days')->nullable()->after('year');
            $table->decimal('daily_rate', 10, 2)->nullable()->after('working_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropIndex(['staff_id']);
            $table->dropColumn(['staff_id', 'working_days', 'daily_rate']);
        });
    }
};
