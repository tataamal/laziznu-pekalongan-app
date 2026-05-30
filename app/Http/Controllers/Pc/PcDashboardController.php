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
        $koinNuByMwc = $this->KoinNuTransactionRepo->getKoinNuPcGroupedByMwc();

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

        $trendKoinNu = $this->KoinNuTransactionRepo->sumTrendKoinNuPc($months);
        $trendInfaq = $this->infaqPcTransactionRepo->sumTrendInfaqPc($months);

        $distKoinNu = $this->KoinNuDistributionRepo->getDistributionGroupedByPilarPc();
        $distInfaq = $this->infaqPcDistributionRepo->getDistributionGroupedByPilarPc();

        // 3. Data Semua Transaksi
        $koinNuIncomes = $this->KoinNuTransactionRepo->getLatestApprovedKoinNuPcTransactions(50);
        $koinNuExpenses = $this->KoinNuDistributionRepo->getLatestApprovedKoinNuDistributionsPc(50);
        $infaqIncomes = $this->infaqPcTransactionRepo->getLatestTransactions(50);
        $infaqExpenses = $this->infaqPcDistributionRepo->getLatestDistributions(50);

        $allTransactions = collect();

        foreach ($koinNuIncomes as $trx) {
            $allTransactions->push([
                'kode' => $trx->transaction_code,
                'tanggal' => $trx->date,
                'user' => $trx->user ? $trx->user->name : '-',
                'role' => $trx->user ? $trx->user->role : '-',
                'jenis_label' => 'Dana Koin NU PC',
                'jenis_filter' => 'pemasukan-koin-nu',
                'nominal' => (float) $trx->koin_nu_pc,
                'tipe' => 'Pemasukan',
                'status' => $trx->status,
            ]);
        }

        foreach ($koinNuExpenses as $dist) {
            $allTransactions->push([
                'kode' => $dist->distribution_code,
                'tanggal' => $dist->date,
                'user' => $dist->user ? $dist->user->name : '-',
                'role' => $dist->user ? $dist->user->role : '-',
                'jenis_label' => $dist->jenis_pilar,
                'jenis_filter' => 'pengeluaran-koin-nu',
                'nominal' => (float) $dist->jumlah_pentasarufan_pc,
                'tipe' => 'Pengeluaran',
                'status' => $dist->status,
            ]);
        }

        foreach ($infaqIncomes as $trx) {
            $allTransactions->push([
                'kode' => $trx->transaction_code,
                'tanggal' => $trx->date,
                'user' => $trx->user ? $trx->user->name : '-',
                'role' => $trx->user ? $trx->user->role : '-',
                'jenis_label' => $trx->jenis_infaq,
                'jenis_filter' => 'pemasukan-infaq',
                'nominal' => (float) $trx->pemasukan_infaq_bersih,
                'tipe' => 'Pemasukan',
                'status' => 'validated',
            ]);
        }

        foreach ($infaqExpenses as $dist) {
            $allTransactions->push([
                'kode' => $dist->distribution_code,
                'tanggal' => $dist->date,
                'user' => $dist->user ? $dist->user->name : '-',
                'role' => $dist->user ? $dist->user->role : '-',
                'jenis_label' => $dist->jenis_pilar,
                'jenis_filter' => 'pengeluaran-infaq',
                'nominal' => (float) $dist->jumlah_total_distribusi,
                'tipe' => 'Pengeluaran',
                'status' => 'validated',
            ]);
        }

        $latestTransactions = $allTransactions->sortByDesc('tanggal')->values()->take(100);

        // Chart Data Json
        $chartDataJson = json_encode([
            'trend' => [
                'labels' => $trendLabels->all(),
                'koin_nu' => $trendKoinNu->map(fn($v) => (float) $v)->all(),
                'infaq' => $trendInfaq->map(fn($v) => (float) $v)->all(),
            ],
            'distribution' => [
                'koin_nu' => [
                    'labels' => $distKoinNu['labels']->all(),
                    'data' => $distKoinNu['data']->map(fn($v) => (float) $v)->all()
                ],
                'infaq' => [
                    'labels' => $distInfaq['labels']->all(),
                    'data' => $distInfaq['data']->map(fn($v) => (float) $v)->all()
                ]
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
            'latestTransactions',
            'koinNuByMwc'
        ));
    }
}