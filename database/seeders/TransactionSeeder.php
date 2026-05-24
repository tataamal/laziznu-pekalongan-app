<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\KoinNuTransaction;
use App\Models\KoinNuDistribution;
use App\Models\InfaqPcTransaction;
use App\Models\InfaqPcDistribution;
use App\Models\InfaqMwcTransaction;
use App\Models\InfaqMwcDistribution;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('role', 'ranting')->first();

        if (!$user || !$user->ranting || !$user->wilayah) {
            $this->command->warn('TransactionSeeder dilewati: user ranting tidak valid.');
            return;
        }

        $ranting = $user->ranting;
        $wilayah = $user->wilayah;
        $baseDate = Carbon::now()->subMonths(5);
        $jasa_petugas_per_kaleng = 1000;
        $statuses = ['approved', 'approved', 'approved', 'pending', 'rejected'];
        $pilars = ['NU Care Cerdas', 'NU Care Sehat', 'NU Care Hijau', 'NU Care Berdaya', 'NU Care Damai'];

        $incomeCount = KoinNuTransaction::count();

        for ($i = 1; $i <= 15; $i++) {
            $incomeCount++;
            $jumlah_kaleng = rand(1, 20);
            $pemasukan_koin_nu_kotor = rand(50, 200) * 1000;
            $jasa_petugas = $jasa_petugas_per_kaleng * $jumlah_kaleng;
            $pemasukan_koin_nu_bersih = $pemasukan_koin_nu_kotor - $jasa_petugas;

            // Pembagian dengan residual allocation supaya selalu balance
            $koin_nu_mwc = (int) round($pemasukan_koin_nu_bersih * 0.35);
            $koin_nu_pc = (int) round($pemasukan_koin_nu_bersih * 0.05);
            $koin_nu_ranting = $pemasukan_koin_nu_bersih - $koin_nu_mwc - $koin_nu_pc;

            $hak_amil_ranting = (int) round($koin_nu_ranting * 0.2);
            $hak_amil_mwc = (int) round($koin_nu_mwc * 0.2);
            $hak_amil_pc = (int) round($koin_nu_pc * 0.2);

            $dana_dapat_digunakan_ranting = $koin_nu_ranting - $hak_amil_ranting;
            $dana_dapat_digunakan_mwc = $koin_nu_mwc - $hak_amil_mwc;
            $dana_dapat_digunakan_pc = $koin_nu_pc - $hak_amil_pc;

            KoinNuTransaction::create([
                'user_id' => $user->id,
                'ranting_id' => $ranting->id,
                'wilayah_id' => $wilayah->id,
                'transaction_code' => 'ICM' . str_pad($incomeCount, 5, '0', STR_PAD_LEFT),
                'date' => $baseDate->copy()->addDays(rand(0, 150)),
                'jumlah_kaleng' => $jumlah_kaleng,
                'pemasukan_koin_nu_kotor' => $pemasukan_koin_nu_kotor,
                'jasa_petugas' => $jasa_petugas,
                'pemasukan_koin_nu_bersih' => $pemasukan_koin_nu_bersih,
                'koin_nu_ranting' => $koin_nu_ranting,
                'koin_nu_mwc' => $koin_nu_mwc,
                'koin_nu_pc' => $koin_nu_pc,
                'hak_amil_ranting' => $hak_amil_ranting,
                'hak_amil_mwc' => $hak_amil_mwc,
                'hak_amil_pc' => $hak_amil_pc,
                'dana_dapat_digunakan_ranting' => $dana_dapat_digunakan_ranting,
                'dana_dapat_digunakan_mwc' => $dana_dapat_digunakan_mwc,
                'dana_dapat_digunakan_pc' => $dana_dapat_digunakan_pc,
                'status' => $statuses[array_rand($statuses)],
            ]);
        }

        $pilars = ['NU Care Cerdas', 'NU Care Sehat', 'NU Care Hijau', 'NU Care Berdaya', 'NU Care Damai'];

        // 1. Koin NU Distribution
        $distKoinCount = KoinNuDistribution::count();
        for ($i = 1; $i <= 10; $i++) {
            $distKoinCount++;
            KoinNuDistribution::create([
                'user_id' => $user->id,
                'distribution_code' => 'DKN' . str_pad($distKoinCount, 5, '0', STR_PAD_LEFT),
                'date' => $baseDate->copy()->addDays(rand(0, 150)),
                'jenis_pilar' => $pilars[array_rand($pilars)],
                'deskripsi' => 'Pentasarufan Koin NU ' . $i,
                'jumlah_pentasarufan_ranting' => rand(100000, 500000),
                'jumlah_pentasarufan_mwc' => rand(100000, 500000),
                'jumlah_pentasarufan_pc' => rand(100000, 500000),
                'jumlah_penerima_manfaat_ranting' => rand(5, 20),
                'jumlah_penerima_manfaat_mwc' => rand(5, 20),
                'jumlah_penerima_manfaat_pc' => rand(5, 20),
                'file_dokumentasi' => 'default.jpg',
                'status' => 'approved',
            ]);
        }

        // Fetch MWC and PC users
        $userMwc = User::where('role', 'mwc')->first();
        $userPc = User::where('role', 'pc')->first();

        // 2. Infaq MWC Transactions & Distributions
        if ($userMwc) {
            for ($i = 1; $i <= 10; $i++) {
                $kotor = rand(500000, 2000000);
                $jasa = $kotor * 0.1;
                $bersih = $kotor - $jasa;
                $amil = $bersih * 0.2;
                $digunakan = $bersih - $amil;

                InfaqMwcTransaction::create([
                    'user_id' => $userMwc->id,
                    'wilayah_id' => $wilayah->id,
                    'transaction_code' => 'IMW' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'date' => $baseDate->copy()->addDays(rand(0, 150)),
                    'jenis_infaq' => 'Infaq Umum',
                    'keterangan' => 'Pemasukan Infaq MWC ' . $i,
                    'pemasukan_infaq_kotor' => $kotor,
                    'jasa_petugas' => $jasa,
                    'pemasukan_infaq_bersih' => $bersih,
                    'hak_amil' => $amil,
                    'infaq_yang_dapat_digunakan' => $digunakan,
                ]);

                InfaqMwcDistribution::create([
                    'user_id' => $userMwc->id,
                    'wilayah_id' => $wilayah->id,
                    'distribution_code' => 'DMW' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'date' => $baseDate->copy()->addDays(rand(0, 150)),
                    'jenis_pilar' => $pilars[array_rand($pilars)],
                    'deskripsi' => 'Pentasarufan Infaq MWC ' . $i,
                    'jumlah_penerima_manfaat' => rand(10, 50),
                    'keterangan' => 'Kegiatan distribusi MWC ' . $i,
                    'jumlah_total_distribusi' => rand(100000, 500000),
                    'file_dokumentasi' => 'default.jpg',
                ]);
            }
        }

        // 3. Infaq PC Transactions & Distributions
        if ($userPc) {
            for ($i = 1; $i <= 10; $i++) {
                $kotor = rand(1000000, 5000000);
                $jasa = $kotor * 0.1;
                $bersih = $kotor - $jasa;
                $amil = $bersih * 0.2;
                $digunakan = $bersih - $amil;

                InfaqPcTransaction::create([
                    'user_id' => $userPc->id,
                    'transaction_code' => 'IPC' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'date' => $baseDate->copy()->addDays(rand(0, 150)),
                    'jenis_infaq' => 'Infaq Umum',
                    'keterangan' => 'Pemasukan Infaq PC ' . $i,
                    'pemasukan_infaq_kotor' => $kotor,
                    'jasa_petugas' => $jasa,
                    'pemasukan_infaq_bersih' => $bersih,
                    'hak_amil' => $amil,
                    'infaq_yang_dapat_digunakan' => $digunakan,
                ]);

                InfaqPcDistribution::create([
                    'user_id' => $userPc->id,
                    'distribution_code' => 'DPC' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'date' => $baseDate->copy()->addDays(rand(0, 150)),
                    'jenis_pilar' => $pilars[array_rand($pilars)],
                    'jumlah_penerima_manfaat' => rand(20, 100),
                    'keterangan' => 'Kegiatan distribusi PC ' . $i,
                    'jumlah_total_distribusi' => rand(500000, 1000000),
                    'file_dokumentasi' => 'default.jpg',
                ]);
            }
        }
    }
}
