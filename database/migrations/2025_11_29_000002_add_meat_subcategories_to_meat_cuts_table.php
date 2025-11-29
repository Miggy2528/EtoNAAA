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
            $table->string('meat_subtype')->nullable()->after('meat_type');
            $table->string('quality_grade')->nullable()->after('quality');
            $table->string('preparation_style')->nullable()->after('preparation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meat_cuts', function (Blueprint $table) {
            $table->dropColumn(['meat_subtype', 'quality_grade', 'preparation_style']);
        });
    }
};
