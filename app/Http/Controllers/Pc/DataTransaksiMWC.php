<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\InfaqMwcTransactionRepository;
use App\Repositories\InfaqMwcDistributionRepository;
use App\Models\Wilayah;

class DataTransaksiMWC extends Controller
{
    protected InfaqMwcTransactionRepository $transactionRepo;
    protected InfaqMwcDistributionRepository $distributionRepo;

    public function __construct(
        InfaqMwcTransactionRepository $transactionRepo,
        InfaqMwcDistributionRepository $distributionRepo
    ) {
        $this->transactionRepo = $transactionRepo;
        $this->distributionRepo = $distributionRepo;
    }

    public function index(Request $request)
    {
        $wilayahId = $request->query('wilayah_id') ? (int) $request->query('wilayah_id') : null;
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();

        $transactionType = $request->query('transaction_type');

        $pemasukans = collect();
        if ($transactionType !== 'Pengeluaran') {
            $transactions = $this->transactionRepo->getTransactions($wilayahId);
            $pemasukans = $transactions->map(function ($item) {
                $item->transaction_type = 'Pemasukan';
                $item->transaction_date = $item->date;
                $item->infaq_type = $item->jenis_infaq;
                $item->gross_amount = $item->pemasukan_infaq_kotor;
                $item->allowed_budget = $item->infaq_yang_dapat_digunakan;
                return $item;
            });
        }

        $pengeluarans = collect();
        if ($transactionType !== 'Pemasukan') {
            $distributions = $this->distributionRepo->getDistributions($wilayahId);
            $pengeluarans = $distributions->map(function ($item) {
                $item->transaction_type = 'Pengeluaran';
                $item->transaction_date = $item->date;
                $item->infaq_type = $item->jenis_pilar;
                $item->gross_amount = $item->jumlah_total_distribusi;
                $item->penerima_manfaat = $item->jumlah_penerima_manfaat;
                $item->transaction_code = $item->distribution_code;
                return $item;
            });
        }

        $items = $pemasukans->concat($pengeluarans)->sortByDesc('transaction_date')->values();

        return view('pc.data-transaksi-mwc', compact('items', 'pemasukans', 'pengeluarans', 'wilayahs', 'wilayahId'));
    }
}
