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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'customer', 'supplier'])
                ->default('customer')
                ->after('email');
                
            $table->enum('status', ['active', 'inactive', 'suspended'])
                ->default('active')
                ->after('role');
                
            $table->softDeletes(); // For account deactivation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status']);
            $table->dropSoftDeletes();
        });
    }
}; 