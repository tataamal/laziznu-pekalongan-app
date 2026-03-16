<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PcDashboardController extends Controller
{
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();

        // 1. Data untuk Card
        $totalSaldoPc = \App\Models\InfaqTransaction::where('user_id', $userId)->sum('allowed_budget');
        $totalMwcUsers = \App\Models\User::where('role', 'mwc')->count();
        $totalRantingUsers = \App\Models\User::where('role', 'ranting')->count();

        // 2. Data untuk Trend (6 Bulan Terakhir)
        $months = collect(range(5, 0))->map(function($i) { return now()->subMonths($i)->format('Y-m'); });
        $trendLabels = $months->map(function($m) { return \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M'); });

        $trendMwc = $months->map(function($m) {
            return \App\Models\InfaqTransaction::whereHas('user', function($q) { $q->where('role', 'mwc'); })
                ->where('transaction_type', 'Pemasukan')
                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$m])
                ->sum('gross_amount');
        });

        $trendPcIncome = $months->map(function($m) use ($userId) {
            return \App\Models\InfaqTransaction::where('user_id', $userId)
                ->where('transaction_type', 'Pemasukan')
                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$m])
                ->sum('gross_amount');
        });

        $trendPcExpense = $months->map(function($m) use ($userId) {
            return \App\Models\InfaqTransaction::where('user_id', $userId)
                ->where('transaction_type', 'Pengeluaran')
                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$m])
                ->sum('gross_amount');
        });

        $trendRanting = $months->map(function($m) {
            return \App\Models\Income::whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$m])
                ->sum('gross_profit');
        });

        // 3. Distribusi Pengeluaran Infaq (Khusus User PC)
        $pcExpenseData = \App\Models\InfaqTransaction::where('user_id', $userId)
            ->where('transaction_type', 'Pengeluaran')
            ->select('infaq_type', \Illuminate\Support\Facades\DB::raw('SUM(gross_amount) as total'))
            ->groupBy('infaq_type')
            ->get();
        
        $distLabels = $pcExpenseData->pluck('infaq_type');
        $distData = $pcExpenseData->pluck('total');

        // Table Data (Latest 10 transactions for PC only)
        $latestTransactions = \App\Models\InfaqTransaction::where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get()
            ->map(function($trx) {
                return [
                    'kode' => $trx->transaction_code,
                    'tanggal' => $trx->transaction_date,
                    'user' => 'PC - Anda',
                    'role' => 'pc',
                    'jenis_label' => $trx->infaq_type,
                    'jenis_filter' => strtolower($trx->transaction_type),
                    'nominal' => (float) $trx->gross_amount,
                    'tipe' => $trx->transaction_type,
                    'status' => 'validated'
                ];
            });

        // Chart Data Json
        $chartDataJson = json_encode([
            'trend' => [
                'labels' => $trendLabels->all(),
                'mwc' => $trendMwc->map(fn($v) => (float)$v)->all(),
                'pc_income' => $trendPcIncome->map(fn($v) => (float)$v)->all(),
                'pc_expense' => $trendPcExpense->map(fn($v) => (float)$v)->all(),
                'ranting' => $trendRanting->map(fn($v) => (float)$v)->all()
            ],
            'distribution' => [
                'labels' => $distLabels->all(),
                'data' => $distData->map(fn($v) => (float)$v)->all()
            ]
        ]);

        return view('pc.dashboard', compact(
            'totalSaldoPc', 'totalMwcUsers', 'totalRantingUsers',
            'chartDataJson', 'latestTransactions'
        ));
    }
}