<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("infaq_mwc_transactions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->restrictOnDelete();
            $table->foreignId("wilayah_id")->nullable()->constrained("wilayahs")->nullOnDelete();
            $table->string("transaction_code")->unique();
            $table->date("date");
            $table->string("jenis_infaq");
            $table->text("keterangan");
            $table->integer("pemasukan_infaq_kotor");
            $table->integer("jasa_petugas");
            $table->integer("pemasukan_infaq_bersih");
            $table->integer("hak_amil");
            $table->integer("infaq_yang_dapat_digunakan");

            // Indexes for reporting & filtering
            $table->index(["wilayah_id", "date"]);
            $table->index(["user_id", "date"]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("infaq_mwc_transactions");
    }
};
