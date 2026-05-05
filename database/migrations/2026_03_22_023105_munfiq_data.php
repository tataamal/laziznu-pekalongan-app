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
        Schema::create('data_munfiqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_ranting_id')->references("id")->on("data_rantings")->onDelete("cascade");
            $table->string('nama');
            $table->string('kode_kaleng');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat')->nullable();
            $table->enum('status', ['Aktif', 'Pasif']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_munfiqs');
    }
};
