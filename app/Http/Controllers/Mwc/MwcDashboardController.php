<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\KoinNuTransactionRepository;
use App\Repositories\KoinNuDistributionRepository;
use App\Repositories\InfaqMwcTransactionRepository;
use App\Repositories\InfaqMwcDistributionRepository;

class MwcDashboardController extends Controller
{
    public function __construct(
        protected KoinNuTransactionRepository $koinNuTransactionRepository,
        protected KoinNuDistributionRepository $koinNuDistributionRepository,
        protected InfaqMwcTransactionRepository $infaqMwcTransactionRepository,
        protected InfaqMwcDistributionRepository $infaqMwcDistributionRepository,
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $wilayahId = $user->wilayah_id;
        $wilayahName = $user->wilayah ? $user->wilayah->nama_wilayah : 'Semua Wilayah';
        $infaqTransactions = $this->infaqMwcTransactionRepository->getTransactions($wilayahId);
        $rantingIncomes = $this->koinNuTransactionRepository->getKoinNuMwc($wilayahId);
        $rantingIncomes->load('ranting', 'user.ranting');
        $koinNuByRanting = $this->koinNuTransactionRepository->getKoinNuMwcGroupedByRanting($wilayahId);
        $pendingIncomesCount = $this->koinNuTransactionRepository->getCountPending($wilayahId);
        $pendingDistributionsCount = $this->koinNuDistributionRepository->getCountPending($wilayahId);

        $totalPemasukanInfaqMwc = $this->infaqMwcTransactionRepository->getTotalPemasukan($wilayahId);
        $totalPengeluaranInfaqMwc = $this->infaqMwcDistributionRepository->getTotalPengeluaran($wilayahId);

        $koinNuDistributions = $this->koinNuDistributionRepository->getDistributionsMwc($wilayahId)->filter(function ($item) {
            return $item->jumlah_pentasarufan_mwc > 0;
        });
        $koinNuDistributions->load('ranting', 'user.ranting');
        $totalPengeluaranKoinNuMwc = $koinNuDistributions->sum('jumlah_pentasarufan_mwc');
        $totalKoinNuWilayah = $rantingIncomes->sum('koin_nu_mwc');

        $hakAmilInfaqMwc = $this->infaqMwcTransactionRepository->getHakAmilMwc($wilayahId);
        $hakAmilKoinNuMwc = $rantingIncomes->sum('hak_amil_mwc');
        $dana_koin_nu_dapat_digunakan_mwc = $rantingIncomes->sum('dana_dapat_digunakan_mwc');
        $infaq_dapat_digunakan_mwc = $this->infaqMwcTransactionRepository->getInfaqDapatDigunakanMwc($wilayahId);

        $months = collect(range(5, 0))->map(function ($i) {
            return now()->subMonthsNoOverflow($i)->format('Y-m'); });
        $lineLabels = $months->map(function ($m) {
            return Carbon::createFromFormat('Y-m', $m)->translatedFormat('M'); });

        // Fetch distributions for expense data
        $infaqDistributions = $this->infaqMwcDistributionRepository->getDistributions($wilayahId);

        // 1. Line Chart Data (4 Series)
        $lineDataIncomeKoin = $months->map(function ($m) use ($rantingIncomes) {
            return $rantingIncomes->filter(function ($trx) use ($m) {
                return Carbon::parse($trx->date)->format('Y-m') === $m;
            })->sum('koin_nu_mwc');
        });

        $lineDataExpenseKoin = $months->map(function ($m) use ($koinNuDistributions) {
            return $koinNuDistributions->filter(function ($trx) use ($m) {
                return Carbon::parse($trx->date)->format('Y-m') === $m;
            })->sum('jumlah_pentasarufan_mwc');
        });

        $lineDataIncomeInfaq = $months->map(function ($m) use ($infaqTransactions) {
            return $infaqTransactions->filter(function ($trx) use ($m) {
                return Carbon::parse($trx->date)->format('Y-m') === $m;
            })->sum('pemasukan_infaq_bersih');
        });

        $lineDataExpenseInfaq = $months->map(function ($m) use ($infaqDistributions) {
            return $infaqDistributions->filter(function ($trx) use ($m) {
                return Carbon::parse($trx->date)->format('Y-m') === $m;
            })->sum('jumlah_total_distribusi');
        });

        // Breakdown for Koin NU Income per month per ranting
        $lineIncomeKoinBreakdown = $months->map(function ($m) use ($rantingIncomes) {
            return $rantingIncomes->filter(function ($trx) use ($m) {
                return Carbon::parse($trx->date)->format('Y-m') === $m;
            })->groupBy(function ($trx) {
                if ($trx->ranting) {
                    return $trx->ranting->nama_ranting;
                }
                if ($trx->user && $trx->user->ranting) {
                    return $trx->user->ranting->nama_ranting;
                }
                return 'Unknown';
            })->map(function ($group) {
                return $group->sum('koin_nu_mwc');
            });
        });

        // Breakdown for Koin NU Expense per month per ranting
        $lineExpenseKoinBreakdown = $months->map(function ($m) use ($koinNuDistributions) {
            return $koinNuDistributions->filter(function ($trx) use ($m) {
                return Carbon::parse($trx->date)->format('Y-m') === $m;
            })->groupBy(function ($trx) {
                if ($trx->ranting) {
                    return $trx->ranting->nama_ranting;
                }
                if ($trx->user && $trx->user->ranting) {
                    return $trx->user->ranting->nama_ranting;
                }
                return 'Transaksi MWC';
            })->map(function ($group) {
                return $group->sum('jumlah_pentasarufan_mwc');
            });
        });

        // 2. Pie/Donut Chart: Distribusi Pentasarufan (Toggleable)
        $pieKoinLabels = $koinNuDistributions->pluck('jenis_pilar')->filter()->unique()->values();
        $pieKoinData = $pieKoinLabels->map(function ($type) use ($koinNuDistributions) {
            return $koinNuDistributions->where('jenis_pilar', $type)->sum('jumlah_pentasarufan_mwc');
        });

        $pieInfaqLabels = $infaqDistributions->pluck('jenis_pilar')->filter()->unique()->values();
        $pieInfaqData = $pieInfaqLabels->map(function ($type) use ($infaqDistributions) {
            return $infaqDistributions->where('jenis_pilar', $type)->sum('jumlah_total_distribusi');
        });

        // Format to JSON for charts
        $chartDataJson = json_encode([
            'line' => [
                'labels' => $lineLabels,
                'income_koin' => $lineDataIncomeKoin,
                'expense_koin' => $lineDataExpenseKoin,
                'income_infaq' => $lineDataIncomeInfaq,
                'expense_infaq' => $lineDataExpenseInfaq,
                'income_koin_breakdown' => $lineIncomeKoinBreakdown,
                'expense_koin_breakdown' => $lineExpenseKoinBreakdown
            ],
            'pie' => [
                'koin' => [
                    'labels' => $pieKoinLabels,
                    'data' => $pieKoinData
                ],
                'infaq' => [
                    'labels' => $pieInfaqLabels,
                    'data' => $pieInfaqData
                ]
            ]
        ]);

        // Table Data (Filtered by wilayah)
        $latestIncomes = $this->koinNuTransactionRepository->getKoinNuMwc($wilayahId)->take(50);
        $latestDistributions = $this->koinNuDistributionRepository->getDistributionsMwc($wilayahId)->filter(function ($item) {
            return $item->jumlah_pentasarufan_mwc > 0;
        })->take(50);
        $latestInfaqTransactions = $this->infaqMwcTransactionRepository->getTransactions($wilayahId)->take(50);
        $latestInfaqDistributions = $this->infaqMwcDistributionRepository->getDistributions($wilayahId)->take(50);

        $latestTransactions = collect();
        foreach ($latestIncomes as $inc) {
            $latestTransactions->push([
                'kode' => $inc->transaction_code,
                'tanggal' => $inc->date,
                'user' => $inc->user ? $inc->user->name : '-',
                'role' => $inc->user ? $inc->user->role : '-',
                'jenis_label' => 'Dana Ranting',
                'jenis_filter' => 'koin',
                'nominal' => $inc->koin_nu_mwc,
                'status' => $inc->status,
                'penerima' => null,
                'tipe_transaksi' => 'Pemasukan Koin NU',
            ]);
        }
        foreach ($latestDistributions as $dst) {
            $latestTransactions->push([
                'kode' => $dst->distribution_code,
                'tanggal' => $dst->date,
                'user' => $dst->user ? $dst->user->name : '-',
                'role' => $dst->user ? $dst->user->role : '-',
                'jenis_label' => $dst->jenis_pilar,
                'jenis_filter' => 'koin',
                'nominal' => $dst->jumlah_pentasarufan_mwc,
                'status' => 'validated',
                'penerima' => $dst->jumlah_penerima_manfaat_mwc,
                'tipe_transaksi' => 'Pentasarufan Koin NU',
            ]);
        }
        foreach ($latestInfaqTransactions as $infaq) {
            $latestTransactions->push([
                'kode' => $infaq->transaction_code,
                'tanggal' => $infaq->date,
                'user' => $infaq->user ? $infaq->user->name : '-',
                'role' => $infaq->user ? $infaq->user->role : '-',
                'jenis_label' => $infaq->jenis_infaq,
                'jenis_filter' => 'infaq',
                'nominal' => $infaq->infaq_yang_dapat_digunakan,
                'status' => 'validated',
                'penerima' => null,
                'tipe_transaksi' => 'Pemasukan Infaq',
            ]);
        }
        foreach ($latestInfaqDistributions as $infaqDist) {
            $latestTransactions->push([
                'kode' => $infaqDist->distribution_code,
                'tanggal' => $infaqDist->date,
                'user' => $infaqDist->user ? $infaqDist->user->name : '-',
                'role' => $infaqDist->user ? $infaqDist->user->role : '-',
                'jenis_label' => $infaqDist->jenis_pilar,
                'jenis_filter' => 'infaq',
                'nominal' => $infaqDist->jumlah_total_distribusi,
                'status' => 'validated',
                'penerima' => $infaqDist->jumlah_penerima_manfaat,
                'tipe_transaksi' => 'Pengeluaran Infaq',
            ]);
        }
        $latestTransactions = $latestTransactions->sortByDesc('tanggal')->values();

        return view('mwc.dashboard', compact(
            'wilayahName',
            'totalPemasukanInfaqMwc',
            'totalPengeluaranInfaqMwc',
            'totalKoinNuWilayah',
            'pendingIncomesCount',
            'pendingDistributionsCount',
            'chartDataJson',
            'latestTransactions',
            'hakAmilInfaqMwc',
            'hakAmilKoinNuMwc',
            'totalPengeluaranKoinNuMwc',
            'dana_koin_nu_dapat_digunakan_mwc',
            'infaq_dapat_digunakan_mwc',
            'koinNuByRanting'
        ));
    }
}