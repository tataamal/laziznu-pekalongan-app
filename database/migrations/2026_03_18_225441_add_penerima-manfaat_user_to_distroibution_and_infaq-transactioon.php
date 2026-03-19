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
        Schema::table('distributions', function (Blueprint $table) {
            $table->integer('penerima_manfaat')->after('pilar_type')->nullable();
        });

        Schema::table('infaq_transaction', function (Blueprint $table) {
            $table->integer('penerima_manfaat')->after('infaq_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributions', function (Blueprint $table) {
            $table->dropColumn('penerima_manfaat');
        });

        Schema::table('infaq_transaction', function (Blueprint $table) {
            $table->dropColumn('penerima_manfaat');
        });
    }
};
