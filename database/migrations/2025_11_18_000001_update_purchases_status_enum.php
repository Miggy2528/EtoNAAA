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
        // Update the status column comment to reflect new values
        DB::statement("ALTER TABLE purchases MODIFY COLUMN status TINYINT NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Approved, 2=For Delivery, 3=Complete, 4=Received'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE purchases MODIFY COLUMN status TINYINT NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Approved'");
    }
};
