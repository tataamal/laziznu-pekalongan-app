<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\KoinNuTransaction;
use App\Models\KoinNuDistribution;
use App\Services\KoinNuDistributionService;
use App\Repositories\KoinNuDistributionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KoinNuDistributionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KoinNuDistributionService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new KoinNuDistributionService(new KoinNuDistributionRepository());
        
        $this->user = User::factory()->create([
            'role' => 'ranting',
            'ranting_id' => 1,
            'wilayah_id' => 1,
        ]);
    }

    public function test_it_can_create_distribution_if_balance_is_sufficient()
    {
        $this->actingAs($this->user);

        // Tambah saldo Koin NU (Transaction) -> status = approved
        KoinNuTransaction::create([
            'user_id' => $this->user->id,
            'ranting_id' => 1,
            'wilayah_id' => 1,
            'transaction_code' => 'KNU00001',
            'date' => '2026-05-10',
            'jumlah_kaleng' => 10,
            'pemasukan_koin_nu_kotor' => 1000000,
            'jasa_petugas' => 100000,
            'pemasukan_koin_nu_bersih' => 900000,
            'koin_nu_ranting' => 540000,
            'koin_nu_mwc' => 315000,
            'koin_nu_pc' => 45000,
            'dana_dapat_digunakan_ranting' => 432000, // <--- Ini balance yang digunakan
            'dana_dapat_digunakan_mwc' => 252000,
            'dana_dapat_digunakan_pc' => 36000,
            'hak_amil_ranting' => 108000,
            'hak_amil_mwc' => 63000,
            'hak_amil_pc' => 9000,
            'status' => 'approved',
        ]);

        $data = [
            'date' => '2026-05-11',
            'deskripsi' => 'Bantuan Sosial',
            'jenis_pilar' => 'Pendidikan',
            'jumlah_pentasarufan_ranting' => 200000, // < 432.000, jadi valid
            'jumlah_pentasarufan_mwc' => 0,
            'jumlah_pentasarufan_pc' => 0,
            'jumlah_penerima_manfaat_ranting' => 5,
            'jumlah_penerima_manfaat_mwc' => 0,
            'jumlah_penerima_manfaat_pc' => 0,
            'file_dokumentasi' => 'distributions/test.webp',
        ];

        $distribution = $this->service->createDistribution($data, 1);

        $this->assertDatabaseHas('koin_nu_distributions', [
            'id' => $distribution->id,
            'jumlah_pentasarufan_ranting' => 200000,
            'status' => 'pending',
        ]);
    }

    public function test_it_throws_exception_if_balance_is_insufficient()
    {
        $this->actingAs($this->user);

        // Tanpa ada saldo awal (Balance = 0)

        $data = [
            'date' => '2026-05-11',
            'deskripsi' => 'Bantuan Sosial',
            'jenis_pilar' => 'Pendidikan',
            'jumlah_pentasarufan_ranting' => 10000, // > 0, invalid
            'file_dokumentasi' => 'distributions/test.webp',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Saldo tidak mencukupi');

        $this->service->createDistribution($data, 1);
    }
}
