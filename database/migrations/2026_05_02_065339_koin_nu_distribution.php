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
        Schema::create("koin_nu_distributions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->restrictOnDelete();
            $table->foreignId("ranting_id")->nullable()->constrained("data_rantings")->nullOnDelete();
            $table->foreignId("wilayah_id")->nullable()->constrained("wilayahs")->nullOnDelete();
            $table->string("distribution_code")->unique();
            $table->date("date");
            $table->string("jenis_pilar");
            $table->text("deskripsi");
            $table->integer("jumlah_pentasarufan_ranting");
            $table->integer("jumlah_pentasarufan_mwc");
            $table->integer("jumlah_pentasarufan_pc");
            $table->integer("jumlah_penerima_manfaat_ranting");
            $table->integer("jumlah_penerima_manfaat_mwc");
            $table->integer("jumlah_penerima_manfaat_pc");
            $table->string("file_dokumentasi");
            $table->enum("status", ["pending", "approved", "rejected"])->default("pending")->nullable();
            $table->string("approved_by")->nullable();
            $table->timestamp("approved_at")->nullable();

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
        Schema::dropIfExists("koin_nu_distributions");
    }
};
