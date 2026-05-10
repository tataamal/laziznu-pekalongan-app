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
        Schema::create("koin_nu_transactions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->restrictOnDelete();
            $table->foreignId("ranting_id")->nullable()->constrained("data_rantings")->nullOnDelete();
            $table->foreignId("wilayah_id")->nullable()->constrained("wilayahs")->nullOnDelete();
            $table->string("transaction_code")->unique();
            $table->date("date");
            $table->integer("jumlah_kaleng");
            $table->integer("pemasukan_koin_nu_kotor");
            $table->integer("jasa_petugas");
            $table->integer("pemasukan_koin_nu_bersih");
            $table->integer("koin_nu_ranting");
            $table->integer("koin_nu_mwc");
            $table->integer("koin_nu_pc");
            $table->integer("dana_dapat_digunakan_ranting");
            $table->integer("dana_dapat_digunakan_mwc");
            $table->integer("dana_dapat_digunakan_pc");
            $table->integer("hak_amil_ranting");
            $table->integer("hak_amil_mwc");
            $table->integer("hak_amil_pc");
            $table->enum("status", ["pending", "approved", "rejected"])->default("pending");

            // Indexes for reporting & filtering
            $table->index(["ranting_id", "date"]);
            $table->index(["wilayah_id", "date"]);
            $table->index(["user_id", "date"]);
            $table->index(["status", "date"]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("koin_nu_transactions");
    }
};
