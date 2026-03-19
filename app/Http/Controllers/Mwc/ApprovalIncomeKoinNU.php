<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;

class ApprovalIncomeKoinNU extends Controller
{
    public function index()
    {
        $items = Income::with('user')
            ->where('status', 'on_process')
            ->whereHas('user', function($query) {
                $query->where('wilayah_id', Auth::user()->wilayah_id);
            })
            ->latest()
            ->get();

        $historyItems = Income::with('user')
            ->whereIn('status', ['validated', 'rejected'])
            ->whereHas('user', function($query) {
                $query->where('wilayah_id', Auth::user()->wilayah_id);
            })
            ->latest()
            ->limit(50)
            ->get();

        return view('mwc.approval-income-koin-nu', compact('items', 'historyItems'));
    }

    public function approve($id)
    {
        $item = Income::findOrFail($id);
        
        $item->update([
            'status' => 'validated',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('mwc.approval-income-koin-nu')
            ->with('success', 'Laporan Pemasukan berhasil disetujui.');
    }

    public function reject($id)
    {
        $item = Income::findOrFail($id);
        
        $item->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('mwc.approval-income-koin-nu')
            ->with('success', 'Laporan Pemasukan telah ditolak.');
    }
}
