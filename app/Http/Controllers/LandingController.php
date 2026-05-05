<?php

namespace App\Http\Controllers;

use App\Models\koin_nu_transaction;
use App\Models\koin_nu_distribution;
use App\Models\infaq_pc_transactions;
use App\Models\infaq_pc_distributions;
use App\Models\infaq_mwc_transactions;
use App\Models\infaq_mwc_distributions;
use App\Models\Wilayah;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $wilayahId = $request->get('wilayah_id', 1);
        $status = $request->get('status', 'approved');

        $wilayahs = Wilayah::all();
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Initial data for view
        $incomeData = $this->getIncomeData($month, $year, $status);
        $distributionData = $this->getDistributionData($month, $year, $status);
        $infaqStats = $this->getInfaqStatsData($month, $year, $wilayahId);

        return view('dashboard', compact(
            'wilayahs', 'months', 'month', 'year', 'wilayahId', 'status',
            'incomeData', 'distributionData', 'infaqStats'
        ));
    }

    public function getIncomeByRanting(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $status = $request->get('status', 'approved');
        
        return response()->json($this->getIncomeData($month, $year, $status));
    }

    public function getDistributionByRanting(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $status = $request->get('status', 'approved');

        return response()->json($this->getDistributionData($month, $year, $status));
    }

    public function getInfaqStats(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $wilayahId = $request->get('wilayah_id', 1);

        return response()->json($this->getInfaqStatsData($month, $year, $wilayahId));
    }

    public function getIncomeData($month, $year, $status = 'approved')
    {
        $query = koin_nu_transaction::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $incomes = $query->orderBy('date', 'desc')->get();

        // Group by user (Ranting)
        $grouped = $incomes->groupBy(function($item) use ($status) {
            return $status === 'all' ? $item->user_id . '_' . $item->status : $item->user_id;
        })->map(function ($items) use ($status) {
            $user = $items->first()->user;
            $rantingName = $user->name;
            if ($status === 'all') {
                $rantingName .= ' (' . ucfirst($items->first()->status) . ')';
            }
            return [
                'ranting' => $rantingName,
                'total' => $items->sum('pemasukan_koin_nu_kotor'),
                'sources' => $items->pluck('status')->unique()->values()->all(),
            ];
        })->values();

        $totalAll = koin_nu_transaction::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'approved')
            ->sum('pemasukan_koin_nu_kotor');

        return [
            'items' => $grouped,
            'total_all' => $totalAll
        ];
    }

    public function getDistributionData($month, $year, $status = 'approved')
    {
        $query = koin_nu_distribution::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $distributions = $query->orderBy('date', 'desc')->get();

        // Group by user (Ranting)
        $grouped = $distributions->groupBy(function($item) use ($status) {
            return $status === 'all' ? $item->user_id . '_' . $item->status : $item->user_id;
        })->map(function ($items) use ($status) {
            $user = $items->first()->user;
            $rantingName = $user->name;
            if ($status === 'all') {
                $rantingName .= ' (' . ucfirst($items->first()->status) . ')';
            }
            return [
                'ranting' => $rantingName,
                'total' => $items->sum('jumlah_pentasarufan'),
                'pillars' => $items->pluck('jenis_pilar')->unique()->values()->all(),
            ];
        })->values();

        $totalAll = koin_nu_distribution::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'approved')
            ->sum('jumlah_pentasarufan');

        return [
            'items' => $grouped,
            'total_all' => $totalAll
        ];
    }

    public function getInfaqStatsData($month, $year, $wilayahId)
    {
        // PC Stats
        $pcIncomeQuery = infaq_pc_transactions::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        $pcIncome = $pcIncomeQuery->sum('pemasukan_infaq_kotor');

        $pcExpenseQuery = infaq_pc_distributions::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        $pcExpense = $pcExpenseQuery->sum('jumlah_total_distribusi');

        // MWC Stats
        $mwcIncomeQuery = infaq_mwc_transactions::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($wilayahId != 'all') {
            $mwcIncomeQuery->whereHas('user', function ($q) use ($wilayahId) {
                $q->where('wilayah_id', $wilayahId);
            });
        }

        $mwcIncome = $mwcIncomeQuery->sum('pemasukan_infaq_kotor');

        $mwcExpenseQuery = infaq_mwc_distributions::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($wilayahId != 'all') {
            $mwcExpenseQuery->whereHas('user', function ($q) use ($wilayahId) {
                $q->where('wilayah_id', $wilayahId);
            });
        }

        $mwcExpense = $mwcExpenseQuery->sum('jumlah_total_distribusi');

        // Debug Log
        Log::info("Dashboard Infaq Stats: Month=$month, Year=$year, Wilayah=$wilayahId, PC Income=$pcIncome, PC Expense=$pcExpense, MWC Income=$mwcIncome, MWC Expense=$mwcExpense");

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
