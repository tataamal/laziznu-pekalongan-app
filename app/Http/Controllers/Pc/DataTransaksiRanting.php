<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\KoinNuTransactionRepository;
use App\Repositories\KoinNuDistributionRepository;
use App\Models\Wilayah;

class DataTransaksiRanting extends Controller
{
    protected KoinNuTransactionRepository $koinNuTransactionRepo;
    protected KoinNuDistributionRepository $koinNuDistributionRepo;

    public function __construct(
        KoinNuTransactionRepository $koinNuTransactionRepo,
        KoinNuDistributionRepository $koinNuDistributionRepo
    ) {
        $this->koinNuTransactionRepo = $koinNuTransactionRepo;
        $this->koinNuDistributionRepo = $koinNuDistributionRepo;
    }

    public function index(Request $request)
    {
        $wilayahId = $request->query('wilayah_id') ? (int) $request->query('wilayah_id') : null;
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();

        $transactionType = $request->query('transaction_type');

        $pemasukans = collect();
        if ($transactionType !== 'Pengeluaran') {
            $transactions = $this->koinNuTransactionRepo->getKoinNuRantingTransactions($wilayahId);
            $pemasukans = $transactions->map(function ($item) {
                $item->gross_profit = $item->pemasukan_koin_nu_kotor;
                $item->net_income = $item->pemasukan_koin_nu_bersih;
                $item->allowed_budget = $item->dana_dapat_digunakan_ranting;
                $item->hak_amil = $item->hak_amil_ranting;
                return $item;
            });
        }

        $pengeluarans = collect();
        if ($transactionType !== 'Pemasukan') {
            $distributions = $this->koinNuDistributionRepo->getKoinNuRantingDistributions($wilayahId);
            $pengeluarans = $distributions->map(function ($item) {
                $item->transaction_code = $item->distribution_code;
                $item->event_name = $item->deskripsi;
                $item->pilar_type = $item->jenis_pilar;
                $item->penerima_manfaat = $item->jumlah_penerima_manfaat_ranting;
                $item->cost_amount = $item->jumlah_pentasarufan_ranting;
                return $item;
            });
        }

        return view('pc.data-transaksi-ranting', compact('pemasukans', 'pengeluarans', 'wilayahs', 'wilayahId'));
    }
}
