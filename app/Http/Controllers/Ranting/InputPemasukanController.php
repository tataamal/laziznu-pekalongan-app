<?php

namespace App\Http\Controllers\Ranting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KoinNuTransactionService;
use App\Repositories\KoinNuTransactionRepository;
use App\Models\KoinNuTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InputPemasukanController extends Controller
{
    protected KoinNuTransactionService $service;
    protected KoinNuTransactionRepository $repository;

    public function __construct(KoinNuTransactionService $service, KoinNuTransactionRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        // Get all transactions for the current ranting
        $rantingId = Auth::user()->ranting_id;
        $items = $this->repository->getKoinNuRanting($rantingId, 'all');

        return view('ranting.income.index', compact('items'));
    }

    public function create()
    {
        return view('ranting.income.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'jumlah_kaleng' => ['required', 'integer', 'min:0'],
            'pemasukan_koin_nu_kotor' => ['required', 'integer', 'min:0'],
            'jasa_petugas' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            // Map request data to service data format
            $data = [
                'date' => $validated['date'],
                'jumlah_kaleng' => $validated['jumlah_kaleng'],
                'pemasukan_koin_nu_kotor' => $validated['pemasukan_koin_nu_kotor'],
                'jasa_petugas' => $validated['jasa_petugas'],
                'status' => 'pending',
            ];

            $this->service->createTransaction($data);

            DB::commit();

            return redirect()
                ->route('ranting.income.index')
                ->with('success', 'Data pemasukan berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $th->getMessage()]);
        }
    }

    public function show($id)
    {
        $item = $this->repository->findById($id);

        return view('ranting.income.show', compact('item'));
    }

    public function edit($id)
    {
        $item = $this->repository->findById($id);
        
        $rantingId = Auth::user()->ranting_id;
        $items = $this->repository->getKoinNuRanting($rantingId);

        return view('ranting.income.index', compact('item', 'items'));
    }

    public function update(Request $request, $id)
    {
        $item = $this->repository->findById($id);

        if ($item->status === 'approved') {
            return redirect()
                ->route('ranting.income.index')
                ->withErrors(['error' => 'Data sudah divalidasi, Hanya bisa dihapus oleh user MWC']);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'jumlah_kaleng' => ['required', 'integer', 'min:0'],
            'pemasukan_koin_nu_kotor' => ['required', 'integer', 'min:0'],
            'jasa_petugas' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'date' => $validated['date'],
                'jumlah_kaleng' => $validated['jumlah_kaleng'],
                'pemasukan_koin_nu_kotor' => $validated['pemasukan_koin_nu_kotor'],
                'jasa_petugas' => $validated['jasa_petugas'],
            ];

            $this->service->updateTransaction($id, $data);

            DB::commit();

            return redirect()
                ->route('ranting.income.index')
                ->with('success', 'Data pemasukan berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengupdate data: ' . $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $this->service->deleteTransaction($id);

        return redirect()
            ->route('ranting.income.index')
            ->with('success', 'Data pemasukan berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:koin_nu_transactions,id'],
        ]);

        $deletedCount = 0;
        foreach ($request->ids as $id) {
            $item = $this->repository->findById($id);
            if ($item && $item->status !== 'approved') {
                $this->service->deleteTransaction($id);
                $deletedCount++;
            }
        }

        return redirect()
            ->route('ranting.income.index')
            ->with('success', $deletedCount . ' data berhasil dihapus.');
    }
}
