<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\koin_nu_transaction;
use App\Models\koin_nu_distribution;
use App\Models\infaq_pc_transactions;
use App\Models\infaq_pc_distributions;
use App\Models\infaq_mwc_transactions;
use App\Models\infaq_mwc_distributions;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user ranting untuk testing
        $user = User::firstOrCreate(
            ['email' => 'ranting@example.com'],
            [
                'name' => 'Admin Ranting A',
                'password' => bcrypt('password'),
                'role' => 'ranting',
                'no_telp' => '08123456789',
            ]
        );

        $incomeCount = koin_nu_transaction::count();
        $baseDate = Carbon::now()->subMonths(5);
        $jasa_petugas_per_kaleng = 1000;

        for ($i = 1; $i <= 15; $i++) {
            $incomeCount++;
            $transactionCode = 'ICM' . str_pad($incomeCount, 5, '0', STR_PAD_LEFT);
            $pemasukan_koin_nu_kotor = 100000;
            $jumlah_kaleng = rand(1,20);
            $jasa_petugas = $jasa_petugas_per_kaleng * $jumlah_kaleng;
            $pemasukan_koin_nu_bersih = $pemasukan_koin_nu_kotor - $jasa_petugas;
            $koin_nu_for_ranting = $pemasukan_koin_nu_bersih * 0.6;
            $koin_nu_for_mwc = $pemasukan_koin_nu_bersih * 0.35;
            $koin_nu_for_pc = $pemasukan_koin_nu_bersih * 0.05;
            $hak_amil_ranting = $koin_nu_for_ranting * 0.2;
            $hak_amil_mwc = $koin_nu_for_mwc * 0.2;
            $hak_amil_pc = $koin_nu_for_pc * 0.2;
            $dana_dapat_digunakan_ranting = $koin_nu_for_ranting - $hak_amil_ranting;
            $dana_dapat_digunakan_mwc = $koin_nu_for_mwc - $hak_amil_mwc;
            $dana_dapat_digunakan_pc = $koin_nu_for_pc - $hak_amil_pc;
            $status = 'approved';

            koin_nu_transaction::create([
                'user_id' => $user->id,
                'transaction_code' => $transactionCode,
                'date' => $baseDate->copy()->addDays(rand(0, 150)),
                'jumlah_kaleng' => $jumlah_kaleng,
                'pemasukan_koin_nu_kotor' => $pemasukan_koin_nu_kotor,
                'jasa_petugas' => $jasa_petugas,
                'pemasukan_koin_nu_bersih' => $pemasukan_koin_nu_bersih,
                'koin_nu_ranting' => $koin_nu_for_ranting,
                'koin_nu_mwc' => $koin_nu_for_mwc,
                'koin_nu_pc' => $koin_nu_for_pc,
                'hak_amil_ranting' => $hak_amil_ranting,
                'hak_amil_mwc' => $hak_amil_mwc,
                'hak_amil_pc' => $hak_amil_pc,
                'dana_dapat_digunakan_ranting' => $dana_dapat_digunakan_ranting,
                'dana_dapat_digunakan_mwc' => $dana_dapat_digunakan_mwc,
                'dana_dapat_digunakan_pc' => $dana_dapat_digunakan_pc,
                'status' => $status,
            ]);
        }

        $pilars = ['NU Care Cerdas', 'NU Care Sehat', 'NU Care Hijau', 'NU Care Berdaya', 'NU Care Damai'];
        
        // 1. Koin NU Distribution
        $distKoinCount = koin_nu_distribution::count();
        for ($i = 1; $i <= 10; $i++) {
            $distKoinCount++;
            koin_nu_distribution::create([
                'user_id' => $user->id,
                'distribution_code' => 'DKN' . str_pad($distKoinCount, 5, '0', STR_PAD_LEFT),
                'date' => $baseDate->copy()->addDays(rand(0, 150)),
                'jenis_pilar' => $pilars[array_rand($pilars)],
                'deskripsi' => 'Pentasarufan Koin NU Ranting ' . $i,
                'jumlah_pentasarufan' => rand(100000, 500000),
                'jumlah_penerima_manfaat' => rand(5, 20),
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

                infaq_mwc_transactions::create([
                    'user_id' => $userMwc->id,
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

                infaq_mwc_distributions::create([
                    'user_id' => $userMwc->id,
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

                infaq_pc_transactions::create([
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

                infaq_pc_distributions::create([
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
