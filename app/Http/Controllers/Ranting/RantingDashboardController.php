<?php

namespace App\Http\Controllers\Ranting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Income;
use App\Models\Distribution;

class RantingDashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $incomes = Income::with('user')->where('user_id', $userId)->get();
        $distributions = Distribution::with('user')->where('user_id', $userId)->get();

        $totalIncome = $incomes->where('status', 'validated')->sum('net_income');
        $totalExpense = $distributions->where('status', 'validated')->sum('cost_amount');
        $totalHakAmil = $incomes->where('status', 'validated')->sum('hak_amil');
        
        $startOfMonth = now()->startOfMonth();
        $transactionsThisMonth = $incomes->where('date', '>=', $startOfMonth->format('Y-m-d'))->count() + 
                                 $distributions->where('date', '>=', $startOfMonth->format('Y-m-d'))->count();
        
        $usableFund = $totalIncome - $totalExpense;
        $months = collect(range(5, 0))->map(function($i) { return now()->subMonths($i)->format('Y-m'); });
        $barLabels = $months->map(function($m) { return Carbon::createFromFormat('Y-m', $m)->translatedFormat('M'); });
        $barData = $months->map(function($m) use ($incomes) { 
            return $incomes->where('status', 'validated')->filter(function($inc) use ($m) { 
                return Carbon::parse($inc->date)->format('Y-m') === $m; 
            })->sum('net_income'); 
        });

        $validatedDistributions = $distributions->where('status', 'validated');
        $pieLabels = $validatedDistributions->pluck('pilar_type')->unique()->values();
        $pieData = $pieLabels->map(function($type) use ($validatedDistributions) {
            return $validatedDistributions->where('pilar_type', $type)->sum('cost_amount');
        });

        if ($pieData->sum() === 0) {
            $pieLabels = collect(['Belum ada data']);
            $pieData = collect([1]);
            $hasPieData = false;
        } else {
            $hasPieData = true;
        }

        $incomeDates = $incomes->where('status', 'validated')->pluck('date')->map(function($d) { return \Carbon\Carbon::parse($d)->format('Y-m-d'); });
        $distributionDates = $distributions->where('status', 'validated')->pluck('date')->map(function($d) { return \Carbon\Carbon::parse($d)->format('Y-m-d'); });

        $allDates = $incomeDates->merge($distributionDates)->unique();
        
        $last7Days = collect(range(6, 0))->map(function($i) { return now()->subDays($i)->format('Y-m-d'); });
        $displayDates = $last7Days->merge($allDates)->unique()->sortDesc()->take(7)->sort()->values();
        
        $trendData = [
            'labels' => $displayDates->map(function($d) { return \Carbon\Carbon::parse($d)->format('d-m-Y'); }),
            'income' => [
                'data' => $displayDates->map(function($date) use ($incomes) {
                    return $incomes->where('status', 'validated')->filter(function($inc) use ($date) {
                        return \Carbon\Carbon::parse($inc->date)->format('Y-m-d') === $date;
                    })->sum('net_income');
                }),
            ],
            'distribution' => [
                'labels' => $displayDates->map(function($date) use ($distributions) {
                    $dist = $distributions->where('status', 'validated')->filter(function($dst) use ($date) {
                        return \Carbon\Carbon::parse($dst->date)->format('Y-m-d') === $date;
                    });
                    return $dist->count() > 0 ? $dist->pluck('pilar_type')->implode(', ') : '-';
                }),
                'data' => $displayDates->map(function($date) use ($distributions) {
                    return $distributions->where('status', 'validated')->filter(function($dst) use ($date) {
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

        return view('ranting.dashboard', compact(
            'totalIncome', 'totalExpense', 'totalHakAmil', 'transactionsThisMonth', 'usableFund', 
            'chartDataJson', 'latestTransactions'
        ));
    }
}