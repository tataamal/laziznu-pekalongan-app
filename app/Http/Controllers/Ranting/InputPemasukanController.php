<?php

namespace App\Http\Controllers\Ranting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InputPemasukanController extends Controller
{
    public function index()
    {
        $items = Income::latest()->get();

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
            'gross_profit' => ['required', 'integer', 'min:0'],
            'operating_expenses' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $calculated = $this->calculateFields(
                $validated['gross_profit'],
                $validated['operating_expenses']
            );

            Income::create([
                'user_id' => Auth::id(),
                'transaction_code' => $this->generateTransactionCode(),
                'date' => $validated['date'],
                'gross_profit' => $validated['gross_profit'],
                'operating_expenses' => $validated['operating_expenses'],
                'net_income' => $calculated['net_income'],
                'percentage' => $calculated['percentage'],
                'allowed_budget' => $calculated['allowed_budget'],
                'hak_amil' => $calculated['hak_amil'],
                'status' => 'on_process',
            ]);

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
        $item = Income::findOrFail($id);

        return view('ranting.income.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Income::findOrFail($id);
        $items = Income::latest()->get();

        return view('ranting.income.index', compact('item', 'items'));
    }

    public function update(Request $request, $id)
    {
        $item = Income::findOrFail($id);

        if ($item->status === 'validated') {
            return redirect()
                ->route('ranting.income.index')
                ->withErrors(['error' => 'Data sudah divalidasi, Hanya bisa dihapus oleh user MWC']);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'gross_profit' => ['required', 'integer', 'min:0'],
            'operating_expenses' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $calculated = $this->calculateFields(
                $validated['gross_profit'],
                $validated['operating_expenses']
            );

            $item->update([
                'date' => $validated['date'],
                'gross_profit' => $validated['gross_profit'],
                'operating_expenses' => $validated['operating_expenses'],
                'net_income' => $calculated['net_income'],
                'percentage' => $calculated['percentage'],
                'allowed_budget' => $calculated['allowed_budget'],
                'hak_amil' => $calculated['hak_amil'],
                // status tidak diubah, tetap mengikuti data sebelumnya
            ]);

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
        $item = Income::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('ranting.income.index')
            ->with('success', 'Data pemasukan berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:incomes,id'],
        ]);

        $deletedCount = Income::whereIn('id', $request->ids)
            ->where('status', '!=', 'validated')
            ->delete();

        return redirect()
            ->route('ranting.income.index')
            ->with('success', $deletedCount . ' data berhasil dihapus.');
    }

    private function calculateFields(int $grossProfit, int $operatingExpenses): array
    {
        $netIncome = max($grossProfit - $operatingExpenses, 0);
        $percentage = 60.00;
        $allowedBudget = (int) round($netIncome * 0.60);
        $hakAmil = (int) round($allowedBudget * 0.20);

        return [
            'net_income' => $netIncome,
            'percentage' => $percentage,
            'allowed_budget' => $allowedBudget,
            'hak_amil' => $hakAmil,
        ];
    }

    private function generateTransactionCode(): string
    {
        $last = Income::where('transaction_code', 'like', 'ICM%')->orderByDesc('id')->first();

        if (!$last || !$last->transaction_code) {
            return 'ICM00001';
        }

        preg_match('/ICM(\d+)/', $last->transaction_code, $matches);

        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $nextNumber = $lastNumber + 1;

        $digitLength = max(5, strlen((string) $nextNumber));

        return 'ICM' . str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
    }

}
