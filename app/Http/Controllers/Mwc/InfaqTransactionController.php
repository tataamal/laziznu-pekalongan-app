<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\InfaqMwcTransactionService;
use App\Services\InfaqMwcDistributionService;
use App\Repositories\InfaqMwcTransactionRepository;
use App\Repositories\InfaqMwcDistributionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InfaqTransactionController extends Controller
{
    protected InfaqMwcTransactionService $transactionService;
    protected InfaqMwcDistributionService $distributionService;
    protected InfaqMwcTransactionRepository $transactionRepo;
    protected InfaqMwcDistributionRepository $distributionRepo;

    public function __construct(
        InfaqMwcTransactionService $transactionService,
        InfaqMwcDistributionService $distributionService,
        InfaqMwcTransactionRepository $transactionRepo,
        InfaqMwcDistributionRepository $distributionRepo
    ) {
        $this->transactionService = $transactionService;
        $this->distributionService = $distributionService;
        $this->transactionRepo = $transactionRepo;
        $this->distributionRepo = $distributionRepo;
    }

    public function index()
    {
        $wilayahId = Auth::user()->wilayah_id;
        $transactions = $this->transactionRepo->getTransactions($wilayahId);
        $distributions = $this->distributionRepo->getDistributions($wilayahId);

        $mappedTransactions = $transactions->map(function ($item) {
            $item->transaction_type = 'Pemasukan';
            $item->transaction_date = $item->date;
            $item->infaq_type = $item->jenis_infaq;
            $item->description = $item->keterangan;
            $item->gross_amount = $item->pemasukan_infaq_kotor;
            $item->penerima_manfaat = 0;
            $item->net_amount = $item->pemasukan_infaq_bersih;
            $item->allowed_budget = $item->infaq_yang_dapat_digunakan;
            $item->hak_amil_mwc = $item->hak_amil;
            $item->is_pemasukan = true;
            $item->jasa_petugas = $item->jasa_petugas;
            return $item;
        });

        $mappedDistributions = $distributions->map(function ($item) {
            $item->transaction_type = 'Pengeluaran';
            $item->transaction_date = $item->date;
            $item->infaq_type = $item->jenis_pilar;
            $item->description = $item->deskripsi;
            $item->gross_amount = $item->jumlah_total_distribusi;
            $item->penerima_manfaat = $item->jumlah_penerima_manfaat;
            $item->net_amount = 0;
            $item->allowed_budget = -$item->jumlah_total_distribusi;
            $item->hak_amil_mwc = 0;
            $item->is_pemasukan = false;
            return $item;
        });

        $items = $mappedTransactions->concat($mappedDistributions)->sortByDesc('date')->values();

        return view('mwc.infaq-transaction', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:Pemasukan,Pengeluaran'],
            'infaq_type' => ['required', 'string', 'max:255'],
            'penerima_manfaat' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'gross_amount' => ['required', 'integer', 'min:0'],
            'jasa_petugas' => ['nullable', 'integer', 'min:0'],
            'file_dokumentasi' => ['required_if:transaction_type,Pengeluaran', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ]);

        DB::beginTransaction();
        try {
            if ($validated['transaction_type'] === 'Pemasukan') {
                $data = [
                    'date' => $validated['transaction_date'],
                    'jenis_infaq' => $validated['infaq_type'],
                    'keterangan' => $validated['description'] ?? '',
                    'pemasukan_infaq_kotor' => $validated['gross_amount'],
                    'jasa_petugas' => $validated['jasa_petugas'] ?? 0,
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
                    'deskripsi' => $validated['description'] ?? '',
                    'keterangan' => $validated['description'] ?? '',
                    'jumlah_total_distribusi' => $validated['gross_amount'],
                    'jumlah_penerima_manfaat' => $validated['penerima_manfaat'] ?? 0,
                ];

                if ($request->hasFile('file_dokumentasi')) {
                    $data['file_dokumentasi'] = $this->saveDocumentationFile(
                        $request->file('file_dokumentasi')
                    );
                }

                $this->distributionService->createDistribution($data, Auth::user()->wilayah_id);
            }

            DB::commit();
            return redirect()->route('mwc.infaq-transaction.index')->with('success', 'Data Infaq berhasil disimpan.');
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
            'jasa_petugas' => ['nullable', 'integer', 'min:0'],
            'file_dokumentasi' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ]);

        // Karena ID bisa dari transaksi atau distribusi, kita pisahkan dengan parameter tipe
        // Note: Controller ini perlu penyesuaian route jika update dilakukan dari 2 tabel terpisah
        // Namun kita bisa mencari berdasarkan tipe
        DB::beginTransaction();
        try {
            if ($validated['transaction_type'] === 'Pemasukan') {
                $data = [
                    'date' => $validated['transaction_date'],
                    'jenis_infaq' => $validated['infaq_type'],
                    'keterangan' => $validated['description'] ?? '',
                    'pemasukan_infaq_kotor' => $validated['gross_amount'],
                    'jasa_petugas' => $validated['jasa_petugas'] ?? 0,
                ];
                $this->transactionService->updateTransaction($id, $data);
            } else {
                $data = [
                    'date' => $validated['transaction_date'],
                    'jenis_pilar' => $validated['infaq_type'],
                    'deskripsi' => $validated['description'] ?? '',
                    'jumlah_total_distribusi' => $validated['gross_amount'],
                    'jumlah_penerima_manfaat' => $validated['penerima_manfaat'] ?? 0,
                ];

                if ($request->hasFile('file_dokumentasi')) {
                    $item = $this->distributionRepo->findById($id);
                    if ($item) {
                        $this->deleteDocumentationFile($item->file_dokumentasi);
                    }

                    $data['file_dokumentasi'] = $this->saveDocumentationFile(
                        $request->file('file_dokumentasi')
                    );
                }

                $this->distributionService->updateDistribution($id, $data, Auth::user()->wilayah_id);
            }

            DB::commit();
            return redirect()->route('mwc.infaq-transaction.index')->with('success', 'Data Infaq berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui data: ' . $th->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        // Mencari dari tabel mana ID ini berasal (atau dikirim lewat payload/query)
        $isPemasukan = $request->query('type') === 'Pemasukan';
        
        if ($isPemasukan) {
            $this->transactionService->deleteTransaction($id);
        } else {
            $this->distributionService->deleteDistribution($id);
        }

        return redirect()->route('mwc.infaq-transaction.index')->with('success', 'Data Infaq berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        // Bulk delete juga sulit jika ID dari 2 tabel bisa sama. 
        // Implementasi amannya adalah mengirim tipe, namun untuk sementara 
        // kita abaikan atau arahkan pengguna untuk hapus satuan.
        return redirect()->route('mwc.infaq-transaction.index')->withErrors(['error' => 'Bulk Delete dinonaktifkan pada menu ini setelah pembaruan arsitektur.']);
    }

    private function saveDocumentationFile($file): string
    {
        $basePath = env('UPLOAD_PUBLIC_PATH', public_path());
        $destination = rtrim($basePath, '/') . '/distributions';
    
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
    
        $image = @imagecreatefromstring(file_get_contents($file));
    
        if ($image !== false) {
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($file->getPathname());
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $image = imagerotate($image, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($image, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($image, 90, 0);
                            break;
                    }
                }
            }
    
            $filename = uniqid() . '_' . time() . '.webp';
            $fullPath = $destination . '/' . $filename;
    
            imagewebp($image, $fullPath, 80);
            imagedestroy($image);
    
            return 'distributions/' . $filename;
        }
    
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $file->move($destination, $filename);
    
        return 'distributions/' . $filename;
    }

    private function deleteDocumentationFile(?string $path): void
    {
        if (!$path) {
            return;
        }
    
        $basePath = env('UPLOAD_PUBLIC_PATH', public_path());
        $fullPath = rtrim($basePath, '/') . '/' . ltrim($path, '/');
    
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}
