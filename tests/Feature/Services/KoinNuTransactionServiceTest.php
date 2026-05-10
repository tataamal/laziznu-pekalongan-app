<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\KoinNuTransaction;
use App\Services\KoinNuTransactionService;
use App\Repositories\KoinNuTransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KoinNuTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KoinNuTransactionService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new KoinNuTransactionService(new KoinNuTransactionRepository());
        
        // Buat user dummy (sebagai Ranting)
        $this->user = User::factory()->create([
            'role' => 'ranting',
            'ranting_id' => 1,
            'wilayah_id' => 1,
        ]);
    }

    public function test_it_can_create_koin_nu_transaction_with_correct_calculations()
    {
        // Login sebagai user ranting
        $this->actingAs($this->user);

        // Data input dari form
        $inputData = [
            'date' => '2026-05-10',
            'jumlah_kaleng' => 10,
            'pemasukan_koin_nu_kotor' => 1000000,
            'jasa_petugas' => 100000, // Maka bersih = 900.000
            'status' => 'pending',
        ];

        // Jalankan service
        $transaction = $this->service->createTransaction($inputData);

        // Assert data berhasil masuk database
        $this->assertDatabaseHas('koin_nu_transactions', [
            'id' => $transaction->id,
            'transaction_code' => 'KNU00001',
            'pemasukan_koin_nu_bersih' => 900000, // 1.000.000 - 100.000
        ]);

        // Verifikasi Perhitungan
        // Bersih = 900.000
        // Ranting (60%) = 540.000 -> Hak Amil (20%) = 108.000 -> Dana Digunakan = 432.000
        // MWC (35%) = 315.000 -> Hak Amil (20%) = 63.000 -> Dana Digunakan = 252.000
        // PC (5%) = 45.000 -> Hak Amil (20%) = 9.000 -> Dana Digunakan = 36.000

        $this->assertEquals(540000, $transaction->koin_nu_ranting);
        $this->assertEquals(108000, $transaction->hak_amil_ranting);
        $this->assertEquals(432000, $transaction->dana_dapat_digunakan_ranting);

        $this->assertEquals(315000, $transaction->koin_nu_mwc);
        $this->assertEquals(63000, $transaction->hak_amil_mwc);
        $this->assertEquals(252000, $transaction->dana_dapat_digunakan_mwc);

        $this->assertEquals(45000, $transaction->koin_nu_pc);
        $this->assertEquals(9000, $transaction->hak_amil_pc);
        $this->assertEquals(36000, $transaction->dana_dapat_digunakan_pc);
    }
}
