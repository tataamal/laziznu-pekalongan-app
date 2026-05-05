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
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("distribution_code")->unique();
            $table->date("date");
            $table->string("jenis_pilar");
            $table->text("deskripsi");
            $table->integer("jumlah_pentasarufan");
            $table->integer("jumlah_penerima_manfaat");
            $table->string("file_dokumentasi");
            $table->enum("status", ["pending", "approved", "rejected"])->default("pending");
            $table->string("approved_by")->nullable();
            $table->timestamp("approved_at")->nullable();
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
