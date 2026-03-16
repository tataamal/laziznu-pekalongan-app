<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InfaqTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InfaqTransactionController extends Controller
{
    public function index()
    {
        $items = InfaqTransaction::with('user')
            ->whereHas('user', function($query) {
                $query->where('wilayah_id', Auth::user()->wilayah_id);
            })
            ->latest()
            ->get();
        return view('mwc.infaq-transaction', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:Pemasukan,Pengeluaran'],
            'infaq_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'gross_amount' => ['required', 'integer', 'min:0']
        ]);

        $percentage = 10; // Fixed 10% fee as per new logic
        $net_amount = $validated['gross_amount'] - ($validated['gross_amount'] * $percentage / 100);
        $allowed_budget = $net_amount;

        DB::beginTransaction();
        try {
            InfaqTransaction::create([
                'user_id' => Auth::id(),
                'transaction_code' => $this->generateTransactionCode(),
                'transaction_date' => $validated['transaction_date'],
                'transaction_type' => $validated['transaction_type'],
                'infaq_type' => $validated['infaq_type'],
                'description' => $validated['description'],
                'gross_amount' => $validated['gross_amount'],
                'percentage' => $percentage,
                'net_amount' => (int) $net_amount,
                'allowed_budget' => (int) $allowed_budget,
            ]);

            DB::commit();
            return redirect()->route('mwc.infaq-transaction.index')->with('success', 'Data Infaq berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $th->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $item = InfaqTransaction::findOrFail($id);

        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:Pemasukan,Pengeluaran'],
            'infaq_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'gross_amount' => ['required', 'integer', 'min:0'],
        ]);

        $percentage = 10; // Fixed 10% fee
        $net_amount = $validated['gross_amount'] - ($validated['gross_amount'] * $percentage / 100);
        $allowed_budget = $net_amount;

        $item->update([
            'transaction_date' => $validated['transaction_date'],
            'transaction_type' => $validated['transaction_type'],
            'infaq_type' => $validated['infaq_type'],
            'description' => $validated['description'],
            'gross_amount' => $validated['gross_amount'],
            'percentage' => $percentage,
            'net_amount' => (int) $net_amount,
            'allowed_budget' => (int) $allowed_budget,
        ]);

        return redirect()->route('mwc.infaq-transaction.index')->with('success', 'Data Infaq berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = InfaqTransaction::findOrFail($id);
        $item->delete();

        return redirect()->route('mwc.infaq-transaction.index')->with('success', 'Data Infaq berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:infaq_transaction,id'],
        ]);

        InfaqTransaction::whereIn('id', $request->ids)->delete();

        return redirect()->route('mwc.infaq-transaction.index')->with('success', count($request->ids) . ' data berhasil dihapus.');
    }

    private function generateTransactionCode(): string
    {
        $last = InfaqTransaction::orderByDesc('id')->first();
        if (!$last || !$last->transaction_code) {
            return 'INF00001';
        }

        preg_match('/INF(\d+)/', $last->transaction_code, $matches);
        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $nextNumber = $lastNumber + 1;

        return 'INF' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
