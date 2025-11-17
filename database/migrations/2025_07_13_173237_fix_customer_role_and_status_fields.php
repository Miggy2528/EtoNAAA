<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run if the role column exists
        if (Schema::hasColumn('customers', 'role')) {
            // Set role to 'customer' where null or empty
            DB::table('customers')->whereNull('role')->orWhere('role', '')->update(['role' => 'customer']);
        }
        
        // Only run if the status column exists
        if (Schema::hasColumn('customers', 'status')) {
            // Set status to 'active' where null or empty
            DB::table('customers')->whereNull('status')->orWhere('status', '')->update(['status' => 'active']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for data fix
    }
};