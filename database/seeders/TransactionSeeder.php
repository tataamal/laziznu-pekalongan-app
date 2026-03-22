<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Income;
use App\Models\Distribution;

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
                'telpon' => '08123456789',
            ]
        );

        $incomeCount = Income::count();
        $baseDate = Carbon::now()->subMonths(5);

        for ($i = 1; $i <= 15; $i++) {
            $incomeCount++;
            $transactionCode = 'ICM' . str_pad($incomeCount, 5, '0', STR_PAD_LEFT);
            $grossProfit = rand(500000, 3000000);
            $operational = $grossProfit * 0.1;
            $netIncome = $grossProfit - $operational;
            
            Income::create([
                'user_id' => $user->id,
                'transaction_code' => $transactionCode,
                'date' => $baseDate->copy()->addDays(rand(0, 150)),
                'gross_profit' => $grossProfit,
                'operating_expenses' => $operational,
                'net_income' => $netIncome,
                'percentage' => 10.0,
                'allowed_budget' => $netIncome * 0.8,
                'hak_amil' => $netIncome * 0.2,
                'status' => ['on_process', 'validated', 'rejected'][rand(0, 2)],
            ]);
        }

        $distributionCount = Distribution::count();
        $pilars = ['NU Care Cerdas', 'NU Care Sehat', 'NU Care Hijau', 'NU Care Berdaya', 'NU Care Damai'];

        for ($i = 1; $i <= 10; $i++) {
            $distributionCount++;
            $transactionCode = 'DST' . str_pad($distributionCount, 5, '0', STR_PAD_LEFT);
            
            Distribution::create([
                'user_id' => $user->id,
                'transaction_code' => $transactionCode,
                'date' => $baseDate->copy()->addDays(rand(0, 150)),
                'event_name' => 'Kegiatan Pentasarufan ' . $i,
                'pilar_type' => $pilars[array_rand($pilars)],
                'cost_amount' => rand(100000, 1000000),
                'status' => ['on_process', 'validated', 'rejected'][rand(0, 2)],
            ]);
        }
    }
}
