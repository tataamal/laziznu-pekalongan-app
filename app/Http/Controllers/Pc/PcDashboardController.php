<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Repositories\KoinNuTransactionRepository;
use App\Repositories\KoinNuDistributionRepository;
use App\Repositories\InfaqPcDistributionRepository;
use App\Repositories\InfaqPcTransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class PcDashboardController extends Controller
{
    public function __construct(
        private KoinNuTransactionRepository $KoinNuTransactionRepo,
        private InfaqPcTransactionRepository $infaqPcTransactionRepo,
        private InfaqPcDistributionRepository $infaqPcDistributionRepo,
        private KoinNuDistributionRepository $KoinNuDistributionRepo,
        private UserRepository $userRepo,
    ) {
    }

    public function index()
    {
        // Mengambil data Pemasukan Koin NU PC (Collection)
        $data_koin_nu = $this->KoinNuTransactionRepo->getKoinNuPc();
        $data_distribusi_koin_nu = $this->KoinNuDistributionRepo->getDistributionsPc();

        // Sum
        $total_koin_nu = $this->KoinNuTransactionRepo->sumKoinNuPc();
        $total_koin_nu_distribusi = $this->KoinNuDistributionRepo->sumDistributionsKoinNuPc();
        $total_hak_amil_pc = $this->KoinNuTransactionRepo->getHakAmilPc();
        $dana_dapat_digunakan_pc = $this->KoinNuTransactionRepo->getDanaDapatDigunakanPc();

        // real dana koin nu yang dapat digunakan
        $sisa_dana_koin_nu = $dana_dapat_digunakan_pc - $total_koin_nu_distribusi;

        // Data Infaq PC
        $data_infaq_pc = $this->infaqPcTransactionRepo->getTransactions();
        $data_distribusi_infaq_pc = $this->infaqPcDistributionRepo->getDistributions();

        // sum infaq
        $total_infaq_pc = $this->infaqPcTransactionRepo->sumPemasukan();
        $total_infaq_pc_distribusi = $this->infaqPcDistributionRepo->sumDistributions();
        $total_hak_amil_pc_infaq = $this->infaqPcTransactionRepo->sumHakAmilPc();
        $dana_dapat_digunakan_pc = $this->infaqPcTransactionRepo->sumDanaDapatDigunakanPc();
        $sisa_dana_infaq_pc = $dana_dapat_digunakan_pc - $total_infaq_pc_distribusi;

        // menghitung jumlah user
        $totalMwcUsers = $this->userRepo->totalUserMwc();
        $totalRantingUsers = $this->userRepo->totalRanting();

        $userId = (int) Auth::id();
        $months = $this->infaqPcTransactionRepo->getTrendMonths();
        $trendLabels = $this->infaqPcTransactionRepo->getTrendLabels();

        $trendMwc = $this->infaqPcTransactionRepo->sumTrendMwc($months);
        $trendPcIncome = $this->infaqPcTransactionRepo->sumTrendPcIncome($userId, $months);
        $trendPcExpense = $this->infaqPcDistributionRepo->sumTrendPcExpense($months, $userId);
        $trendRanting = $this->infaqPcTransactionRepo->sumTrendRanting($months);

        $expenseDistribution = $this->infaqPcDistributionRepo->getExpenseDistributionForUser($userId);
        $distLabels = $expenseDistribution['labels'];
        $distData = $expenseDistribution['data'];

        $latestTransactions = $this->infaqPcTransactionRepo->getLatestTransactionsForPc($userId, 10);

        // Chart Data Json
        $chartDataJson = json_encode([
            'trend' => [
                'labels' => $trendLabels->all(),
                'mwc' => $trendMwc->map(fn($v) => (float) $v)->all(),
                'pc_income' => $trendPcIncome->map(fn($v) => (float) $v)->all(),
                'pc_expense' => $trendPcExpense->map(fn($v) => (float) $v)->all(),
                'ranting' => $trendRanting->map(fn($v) => (float) $v)->all()
            ],
            'distribution' => [
                'labels' => $distLabels->all(),
                'data' => $distData->map(fn($v) => (float) $v)->all()
            ]
        ]);

        return view('pc.dashboard', compact(
            // card data
            'total_koin_nu',
            'total_koin_nu_distribusi',
            'total_hak_amil_pc',
            'sisa_dana_koin_nu',
            'total_infaq_pc',
            'total_infaq_pc_distribusi',
            'total_hak_amil_pc_infaq',
            'sisa_dana_infaq_pc',

            // user data
            'totalMwcUsers',
            'totalRantingUsers',

            // chart data
            'chartDataJson',
            'latestTransactions'
        ));
    }
}