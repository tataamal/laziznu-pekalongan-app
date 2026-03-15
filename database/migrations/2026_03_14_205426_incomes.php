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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_code')->index();
            $table->date('date');
            $table->integer('gross_profit');
            $table->integer('operating_expenses');
            $table->integer('net_income');
            $table->decimal('percentage', 5, 2);
            $table->integer('allowed_budget');
            $table->integer('hak_amil');
            $table->enum('status', ['on_process', 'validated', 'rejected'])->default('on_process');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
