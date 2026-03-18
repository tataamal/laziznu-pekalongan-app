<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Distribution;
use App\Models\InfaqTransaction;
use App\Models\Wilayah;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $wilayahId = $request->get('wilayah_id', 1);

        $wilayahs = Wilayah::all();
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Initial data for view
        $incomeData = $this->getIncomeData($month, $year);
        $distributionData = $this->getDistributionData($month, $year);
        $infaqStats = $this->getInfaqStatsData($month, $year, $wilayahId);

        return view('dashboard', compact(
            'wilayahs', 'months', 'month', 'year', 'wilayahId',
            'incomeData', 'distributionData', 'infaqStats'
        ));
    }

    public function getIncomeByRanting(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        return response()->json($this->getIncomeData($month, $year));
    }

    public function getDistributionByRanting(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        return response()->json($this->getDistributionData($month, $year));
    }

    public function getInfaqStats(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $wilayahId = $request->get('wilayah_id', 1);

        return response()->json($this->getInfaqStatsData($month, $year, $wilayahId));
    }

    private function getIncomeData($month, $year)
    {
        $incomes = Income::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        // Group by user (Ranting)
        $grouped = $incomes->groupBy('user_id')->map(function ($items) {
            $user = $items->first()->user;
            return [
                'ranting' => $user->name,
                'total' => $items->sum('net_income'),
                'sources' => $items->pluck('status')->unique()->values()->all(), // Using status as source proxy or similar if source field not found
            ];
        })->values();

        $totalAll = $grouped->sum('total');

        return [
            'items' => $grouped,
            'total_all' => $totalAll
        ];
    }

    private function getDistributionData($month, $year)
    {
        $distributions = Distribution::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        // Group by user (Ranting)
        $grouped = $distributions->groupBy('user_id')->map(function ($items) {
            $user = $items->first()->user;
            return [
                'ranting' => $user->name,
                'total' => $items->sum('cost_amount'),
                'pillars' => $items->pluck('pilar_type')->unique()->values()->all(),
            ];
        })->values();

        $totalAll = $grouped->sum('total');

        return [
            'items' => $grouped,
            'total_all' => $totalAll
        ];
    }

    private function getInfaqStatsData($month, $year, $wilayahId)
    {
        // PC Stats
        $pcIncomeQuery = InfaqTransaction::whereHas('user', function ($q) {
                $q->where('role', 'pc');
            })
            ->where('transaction_type', 'Pemasukan')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year);

        $pcIncome = $pcIncomeQuery->sum('gross_amount');

        $pcExpenseQuery = InfaqTransaction::whereHas('user', function ($q) {
                $q->where('role', 'pc');
            })
            ->where('transaction_type', 'Pengeluaran')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year);

        $pcExpense = $pcExpenseQuery->sum('gross_amount');

        // MWC Stats
        $mwcIncomeQuery = InfaqTransaction::whereHas('user', function ($q) use ($wilayahId) {
                $q->where('role', 'mwc');
                if ($wilayahId != 'all') {
                    $q->where('wilayah_id', $wilayahId);
                }
            })
            ->where('transaction_type', 'Pemasukan')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year);

        $mwcIncome = $mwcIncomeQuery->sum('gross_amount');

        $mwcExpenseQuery = InfaqTransaction::whereHas('user', function ($q) use ($wilayahId) {
                $q->where('role', 'mwc');
                if ($wilayahId != 'all') {
                    $q->where('wilayah_id', $wilayahId);
                }
            })
            ->where('transaction_type', 'Pengeluaran')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year);

        $mwcExpense = $mwcExpenseQuery->sum('gross_amount');

        // Debug Log
        \Illuminate\Support\Facades\Log::info("Dashboard Infaq Stats: Month=$month, Year=$year, Wilayah=$wilayahId, PC Income=$pcIncome, PC Expense=$pcExpense, MWC Income=$mwcIncome, MWC Expense=$mwcExpense");

        return [
            'pc' => [
                'income' => (float)$pcIncome,
                'expense' => (float)$pcExpense,
                'ratio_income' => $pcIncome + $pcExpense > 0 ? round(($pcIncome / ($pcIncome + $pcExpense)) * 100, 1) : 0,
                'ratio_expense' => $pcIncome + $pcExpense > 0 ? round(($pcExpense / ($pcIncome + $pcExpense)) * 100, 1) : 0,
            ],
            'mwc' => [
                'income' => (float)$mwcIncome,
                'expense' => (float)$mwcExpense,
                'ratio_income' => $mwcIncome + $mwcExpense > 0 ? round(($mwcIncome / ($mwcIncome + $mwcExpense)) * 100, 1) : 0,
                'ratio_expense' => $mwcIncome + $mwcExpense > 0 ? round(($mwcExpense / ($mwcIncome + $mwcExpense)) * 100, 1) : 0,
            ]
        ];
    }
}
