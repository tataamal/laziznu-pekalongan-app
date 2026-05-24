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
        Schema::create("InfaqPcTransaction", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("transaction_code")->unique();
            $table->date("date");
            $table->string("jenis_infaq");
            $table->text("keterangan");
            $table->integer("pemasukan_infaq_kotor");
            $table->integer("jasa_petugas");
            $table->integer("pemasukan_infaq_bersih");
            $table->integer("hak_amil");
            $table->integer("infaq_yang_dapat_digunakan");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("InfaqPcTransaction");
    }
};
