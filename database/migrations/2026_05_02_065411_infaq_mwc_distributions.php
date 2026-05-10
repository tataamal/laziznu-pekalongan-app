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
        Schema::create("infaq_mwc_distributions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->restrictOnDelete();
            $table->foreignId("wilayah_id")->nullable()->constrained("wilayahs")->nullOnDelete();
            $table->string("distribution_code")->unique();
            $table->date("date");
            $table->string("jenis_pilar");
            $table->string("deskripsi");
            $table->integer("jumlah_penerima_manfaat");
            $table->text("keterangan");
            $table->integer("jumlah_total_distribusi");
            $table->string("file_dokumentasi")->nullable();

            // Indexes for reporting & filtering
            $table->index(["wilayah_id", "date"]);
            $table->index(["user_id", "date"]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("infaq_mwc_distributions");
    }
};
