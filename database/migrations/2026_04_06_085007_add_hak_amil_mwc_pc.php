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
        Schema::table('infaq_transaction', function (Blueprint $table) {
            $table->decimal('hak_amil_mwc', 15, 2)->default(0)->after('allowed_budget');
            $table->decimal('hak_amil_pc', 15, 2)->default(0)->after('hak_amil_mwc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infaq_transaction', function (Blueprint $table) {
            $table->dropColumn('hak_amil_mwc');
            $table->dropColumn('hak_amil_pc');
        });
    }
};
