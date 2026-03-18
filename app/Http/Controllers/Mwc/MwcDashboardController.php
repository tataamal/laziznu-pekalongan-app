<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Income;
use App\Models\Distribution;
use Illuminate\Support\Facades\Auth;

class MwcDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wilayahId = $user->wilayah_id;
        $wilayahName = $user->wilayah ? $user->wilayah->nama_wilayah : 'Semua Wilayah';

        // 1. Infaq Transactions (MWC's own infaq filtered by wilayah)
        $infaqTransactions = \App\Models\InfaqTransaction::with('user')
            ->whereHas('user', function($q) use ($wilayahId) {
                if ($wilayahId) $q->where('wilayah_id', $wilayahId);
            })->get();

        // 2. Ranting Incomes (Filtered by wilayah and validated for balance)
        $rantingIncomes = Income::with('user')
            ->whereHas('user', function($q) use ($wilayahId) {
                if ($wilayahId) $q->where('wilayah_id', $wilayahId);
            })->where('status', 'validated')->get();

        // 3. Pending Approvals (Filtered by wilayah)
        $pendingIncomesCount = Income::whereHas('user', function($q) use ($wilayahId) {
                if ($wilayahId) $q->where('wilayah_id', $wilayahId);
            })->where('status', 'on_process')->count();

        $pendingDistributionsCount = Distribution::whereHas('user', function($q) use ($wilayahId) {
                if ($wilayahId) $q->where('wilayah_id', $wilayahId);
            })->where('status', 'on_process')->count();

        // Stats Calculation
        $totalInfaqMwc = $infaqTransactions->sum('allowed_budget');
        $totalKoinNuWilayah = $rantingIncomes->sum('hak_amil');

        // --- CHART DATA ---

        // A. Line Chart: Infaq Trend (Pemasukan vs Pengeluaran from MWC Infaq)
        $months = collect(range(5, 0))->map(function($i) { return now()->subMonths($i)->format('Y-m'); });
        $lineLabels = $months->map(function($m) { return \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M'); });
        
        $lineDataIncome = $months->map(function($m) use ($infaqTransactions) {
            return $infaqTransactions->filter(function($trx) use ($m) {
                return \Carbon\Carbon::parse($trx->transaction_date)->format('Y-m') === $m && $trx->transaction_type === 'Pemasukan';
            })->sum('gross_amount');
        });

        $lineDataExpense = $months->map(function($m) use ($infaqTransactions) {
            return $infaqTransactions->filter(function($trx) use ($m) {
                return \Carbon\Carbon::parse($trx->transaction_date)->format('Y-m') === $m && $trx->transaction_type === 'Pengeluaran';
            })->sum('gross_amount');
        });

        // B. Pie/Donut Chart: Distribusi Jenis Infaq (By infaq_type)
        $pieLabels = $infaqTransactions->pluck('infaq_type')->unique()->values();
        $pieData = $pieLabels->map(function($type) use ($infaqTransactions) {
            return $infaqTransactions->where('infaq_type', $type)->sum('gross_amount');
        });
        $hasPieData = $pieData->sum() > 0;

        // C. Ranting Bar Chart (Sum of allowed_budget per Ranting within wilayah)
        $rantingPerformance = $rantingIncomes->groupBy(function($inc) {
            return $inc->user ? $inc->user->name : 'Unknown';
        })->map(function($group) {
            return $group->sum('allowed_budget');
        });

        $barRantingLabels = $rantingPerformance->keys();
        $barRantingValues = $rantingPerformance->values();

        // Format to JSON for charts
        $chartDataJson = json_encode([
            'line' => [
                'labels' => $lineLabels,
                'income' => $lineDataIncome,
                'expense' => $lineDataExpense
            ],
            'pie' => [
                'labels' => $pieLabels,
                'data' => $pieData,
                'isEmpty' => !$hasPieData
            ],
            'ranting' => [
                'labels' => $barRantingLabels,
                'values' => $barRantingValues
            ]
        ]);

        // Table Data (Filtered by wilayah)
        $latestIncomes = Income::with('user')
            ->whereHas('user', function($q) use ($wilayahId) {
                if ($wilayahId) $q->where('wilayah_id', $wilayahId);
            })->latest()->take(50)->get();

        $latestDistributions = Distribution::with('user')
            ->whereHas('user', function($q) use ($wilayahId) {
                if ($wilayahId) $q->where('wilayah_id', $wilayahId);
            })->latest()->take(50)->get();

        $latestTransactions = collect();
        foreach($latestIncomes as $inc) {
            $latestTransactions->push([
                'kode' => $inc->transaction_code,
                'tanggal' => $inc->date,
                'user' => $inc->user ? $inc->user->name : '-',
                'role' => $inc->user ? $inc->user->role : '-',
                'jenis_label' => 'Dana Ranting',
                'jenis_filter' => 'pemasukan',
                'nominal' => $inc->net_income,
                'status' => $inc->status,
            ]);
        }
        foreach($latestDistributions as $dst) {
            $latestTransactions->push([
                'kode' => $dst->transaction_code,
                'tanggal' => $dst->date,
                'user' => $dst->user ? $dst->user->name : '-',
                'role' => $dst->user ? $dst->user->role : '-',
                'jenis_label' => $dst->event_name, 
                'jenis_filter' => 'pengeluaran',
                'nominal' => $dst->cost_amount,
                'status' => $dst->status,
            ]);
        }
        $latestTransactions = $latestTransactions->sortByDesc('tanggal')->values();

        return view('mwc.dashboard', compact(
            'wilayahName', 'totalInfaqMwc', 'totalKoinNuWilayah', 
            'pendingIncomesCount', 'pendingDistributionsCount',
            'chartDataJson', 'latestTransactions'
        ));
    }
}