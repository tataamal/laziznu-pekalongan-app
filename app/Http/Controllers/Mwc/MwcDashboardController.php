<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Income;
use App\Models\Distribution;

class MwcDashboardController extends Controller
{
    public function index()
    {
        $incomes = Income::with('user')->get();
        $distributions = Distribution::with('user')->get();
        $totalIncome = $incomes->sum('net_income');
        $totalExpense = $distributions->sum('cost_amount');
        $totalInfaq = $distributions->sum('cost_amount');
        
        $startOfMonth = now()->startOfMonth();
        $transactionsThisMonth = $incomes->where('date', '>=', $startOfMonth->format('Y-m-d'))->count() + 
                                 $distributions->where('date', '>=', $startOfMonth->format('Y-m-d'))->count();
        
        $usableFund = $totalIncome - $totalExpense;

        // Bar Chart (Pemasukan Bulanan) 6 Bulan terakhir
        $months = collect(range(5, 0))->map(function($i) { return now()->subMonths($i)->format('Y-m'); });
        $barLabels = $months->map(function($m) { return \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M'); });
        $barData = $months->map(function($m) use ($incomes) { 
            return $incomes->filter(function($inc) use ($m) { 
                return \Carbon\Carbon::parse($inc->date)->format('Y-m') === $m; 
            })->sum('net_income'); 
        });

        // Pie Chart (Distribusi Pilar Infaq)
        $pieLabels = $distributions->pluck('pilar_type')->unique()->values();
        $pieData = $pieLabels->map(function($type) use ($distributions) {
            return $distributions->where('pilar_type', $type)->sum('cost_amount');
        });

        // Placeholder if no distribution data
        if ($pieData->sum() === 0) {
            $pieLabels = collect(['Belum ada data']);
            $pieData = collect([1]); // Use 1 as placeholder for donut display
            $hasPieData = false;
        } else {
            $hasPieData = true;
        }

        // Trend Chart (1 Minggu Terakhir)
        $incomeDates = $incomes->pluck('date')->map(function($d) { return \Carbon\Carbon::parse($d)->format('Y-m-d'); });
        $distributionDates = $distributions->pluck('date')->map(function($d) { return \Carbon\Carbon::parse($d)->format('Y-m-d'); });

        $allDates = $incomeDates->merge($distributionDates)->unique();
        
        // Ensure we always have the last 7 days if data is sparse or empty
        $last7Days = collect(range(6, 0))->map(function($i) { return now()->subDays($i)->format('Y-m-d'); });
        $displayDates = $last7Days->merge($allDates)->unique()->sortDesc()->take(7)->sort()->values();
        
        $trendData = [
            'labels' => $displayDates->map(function($d) { return \Carbon\Carbon::parse($d)->format('d-m-Y'); }),
            'income' => [
                'data' => $displayDates->map(function($date) use ($incomes) {
                    return $incomes->filter(function($inc) use ($date) {
                        return \Carbon\Carbon::parse($inc->date)->format('Y-m-d') === $date;
                    })->sum('net_income');
                }),
            ],
            'distribution' => [
                'labels' => $displayDates->map(function($date) use ($distributions) {
                    $dist = $distributions->filter(function($dst) use ($date) {
                        return \Carbon\Carbon::parse($dst->date)->format('Y-m-d') === $date;
                    });
                    return $dist->count() > 0 ? $dist->pluck('pilar_type')->implode(', ') : '-';
                }),
                'data' => $displayDates->map(function($date) use ($distributions) {
                    return $distributions->filter(function($dst) use ($date) {
                        return \Carbon\Carbon::parse($dst->date)->format('Y-m-d') === $date;
                    })->sum('cost_amount');
                }),
            ]
        ];

        // Format to json for chart script
        $chartDataJson = json_encode([
            'bar' => [
                'labels' => $barLabels,
                'data' => $barData
            ],
            'pie' => [
                'labels' => $pieLabels,
                'data' => $pieData,
                'isEmpty' => !$hasPieData
            ],
            'trend' => $trendData
        ]);

        // Table Data (All Transactions for Client-side filtering)
        $latestTransactions = collect();
        foreach($incomes as $inc) {
            $latestTransactions->push([
                'kode' => $inc->transaction_code,
                'tanggal' => $inc->date,
                'user' => $inc->user ? $inc->user->name : '-',
                'role' => $inc->user ? $inc->user->role : '-',
                'jenis_label' => 'Pemasukan Net',
                'jenis_filter' => 'pemasukan',
                'nominal' => $inc->net_income,
                'status' => $inc->status,
            ]);
        }
        foreach($distributions as $dst) {
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
            'totalIncome', 'totalExpense', 'totalInfaq', 'transactionsThisMonth', 'usableFund', 
            'chartDataJson', 'latestTransactions'
        ));
    }
}