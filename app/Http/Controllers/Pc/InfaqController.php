<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\InfaqPcTransactionService;
use App\Services\InfaqPcDistributionService;
use App\Repositories\InfaqPcTransactionRepository;
use App\Repositories\InfaqPcDistributionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InfaqController extends Controller
{
    protected InfaqPcTransactionService $transactionService;
    protected InfaqPcDistributionService $distributionService;
    protected InfaqPcTransactionRepository $transactionRepo;
    protected InfaqPcDistributionRepository $distributionRepo;

    public function __construct(
        InfaqPcTransactionService $transactionService,
        InfaqPcDistributionService $distributionService,
        InfaqPcTransactionRepository $transactionRepo,
        InfaqPcDistributionRepository $distributionRepo
    ) {
        $this->transactionService = $transactionService;
        $this->distributionService = $distributionService;
        $this->transactionRepo = $transactionRepo;
        $this->distributionRepo = $distributionRepo;
    }

    public function index()
    {
        // Fetch Pemasukan
        $transactions = $this->transactionRepo->getTransactions();
        // Fetch Pengeluaran
        $distributions = $this->distributionRepo->getDistributions();

        // Map and combine them to match the old view expectation
        $mappedTransactions = $transactions->map(function ($item) {
            $item->transaction_type = 'Pemasukan';
            $item->transaction_date = $item->date;
            $item->infaq_type = $item->jenis_infaq;
            $item->description = $item->keterangan;
            $item->gross_amount = $item->pemasukan_infaq_kotor;
            $item->penerima_manfaat = 0;
            $item->net_amount = $item->pemasukan_infaq_bersih;
            $item->allowed_budget = $item->infaq_yang_dapat_digunakan;
            $item->hak_amil_pc = $item->hak_amil;
            $item->is_pemasukan = true;
            return $item;
        });

        $mappedDistributions = $distributions->map(function ($item) {
            $item->transaction_type = 'Pengeluaran';
            $item->transaction_date = $item->date;
            $item->infaq_type = $item->jenis_pilar;
            $item->description = $item->keterangan;
            $item->gross_amount = $item->jumlah_total_distribusi;
            $item->penerima_manfaat = $item->jumlah_penerima_manfaat;
            $item->net_amount = 0;
            $item->allowed_budget = -$item->jumlah_total_distribusi;
            $item->hak_amil_pc = 0;
            $item->is_pemasukan = false;
            return $item;
        });

        $items = $mappedTransactions->concat($mappedDistributions)->sortByDesc('date')->values();

        return view('pc.infaq-transaction', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:Pemasukan,Pengeluaran'],
            'infaq_type' => ['required', 'string', 'max:255'],
            'penerima_manfaat' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'gross_amount' => ['required', 'integer', 'min:0']
        ]);

        DB::beginTransaction();
        try {
            if ($validated['transaction_type'] === 'Pemasukan') {
                $data = [
                    'date' => $validated['transaction_date'],
                    'jenis_infaq' => $validated['infaq_type'],
                    'keterangan' => $validated['description'] ?? '',
                    'pemasukan_infaq_kotor' => $validated['gross_amount'],
                    'jasa_petugas' => 0,
                ];
                $this->transactionService->createTransaction($data);
            } else {
                // Pengeluaran
                // Cek jika ini Koin NU, arahkan pengguna ke Koin NU Distribution
                if ($validated['infaq_type'] === 'Saldo Koin NU') {
                    return back()->withInput()->withErrors(['error' => 'Gunakan menu Distribusi Koin NU untuk menyalurkan Saldo Koin NU.']);
                }

                $data = [
                    'date' => $validated['transaction_date'],
                    'jenis_pilar' => $validated['infaq_type'],
                    'keterangan' => $validated['description'] ?? '',
                    'jumlah_total_distribusi' => $validated['gross_amount'],
                    'jumlah_penerima_manfaat' => $validated['penerima_manfaat'] ?? 0,
                ];
                $this->distributionService->createDistribution($data);
            }

            DB::commit();
            return redirect()->route('pc.infaq.index')->with('success', 'Data Infaq PC berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $th->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:Pemasukan,Pengeluaran'],
            'infaq_type' => ['required', 'string', 'max:255'],
            'penerima_manfaat' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'gross_amount' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            if ($validated['transaction_type'] === 'Pemasukan') {
                $data = [
                    'date' => $validated['transaction_date'],
                    'jenis_infaq' => $validated['infaq_type'],
                    'keterangan' => $validated['description'] ?? '',
                    'pemasukan_infaq_kotor' => $validated['gross_amount'],
                ];
                $this->transactionService->updateTransaction($id, $data);
            } else {
                $data = [
                    'date' => $validated['transaction_date'],
                    'jenis_pilar' => $validated['infaq_type'],
                    'keterangan' => $validated['description'] ?? '',
                    'jumlah_total_distribusi' => $validated['gross_amount'],
                    'jumlah_penerima_manfaat' => $validated['penerima_manfaat'] ?? 0,
                ];
                $this->distributionService->updateDistribution($id, $data);
            }

            DB::commit();
            return redirect()->route('pc.infaq.index')->with('success', 'Data Infaq PC berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui data: ' . $th->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $isPemasukan = $request->query('type') === 'Pemasukan';

        if ($isPemasukan) {
            $this->transactionService->deleteTransaction($id);
        } else {
            $this->distributionService->deleteDistribution($id);
        }

        return redirect()->route('pc.infaq.index')->with('success', 'Data Infaq PC berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids && is_array($ids)) {
            foreach ($ids as $val) {
                $parts = explode('-', $val, 2);
                if (count($parts) === 2) {
                    $type = $parts[0];
                    $id = (int)$parts[1];

                    if ($type === 'Pemasukan') {
                        $this->transactionService->deleteTransaction($id);
                    } elseif ($type === 'Pengeluaran') {
                        $this->distributionService->deleteDistribution($id);
                    }
                }
            }
            return redirect()->route('pc.infaq.index')->with('success', 'Data transaksi terpilih berhasil dihapus.');
        }

        return redirect()->route('pc.infaq.index')->withErrors(['error' => 'Tidak ada data yang dipilih untuk dihapus.']);
    }
}
