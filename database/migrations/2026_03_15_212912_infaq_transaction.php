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
        Schema::create('infaq_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_code')->unique();
            $table->date('transaction_date');
            $table->enum('transaction_type', ['Pemasukan', 'Pengeluaran']);
            $table->string('infaq_type');
            $table->text('description')->nullable();
            $table->integer('gross_amount');
            $table->decimal('percentage', 5, 2);
            $table->integer('net_amount');
            $table->integer('allowed_budget');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infaq_transaction');
    }
};
