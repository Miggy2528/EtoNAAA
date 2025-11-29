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
            $table->string('meat_type')->nullable()->after('animal_type');
            $table->string('quality')->nullable()->after('meat_type');
            $table->string('preparation_type')->nullable()->after('quality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meat_cuts', function (Blueprint $table) {
            $table->dropColumn(['meat_type', 'quality', 'preparation_type']);
        });
    }
};
