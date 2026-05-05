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
        Schema::create('data_rantings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->references("id")->on("wilayahs")->onDelete("cascade");
            $table->string('nama_ranting');
            $table->string('kode_ranting');
            $table->string('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_rantings');
    }
};
