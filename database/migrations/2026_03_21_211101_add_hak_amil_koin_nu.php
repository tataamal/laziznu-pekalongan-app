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
        Schema::table('incomes', function (Blueprint $table) {
            $table->integer('hak_amil_mwc')->after('hak_amil');
            $table->integer('hak_amil_pc')->after('hak_amil_mwc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('hak_amil_mwc');
            $table->dropColumn('hak_amil_pc');
        });
    }
};
