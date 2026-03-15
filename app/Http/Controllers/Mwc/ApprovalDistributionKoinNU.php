<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distribution;
use Illuminate\Support\Facades\Auth;

class ApprovalDistributionKoinNU extends Controller
{
    public function index()
    {
        $items = Distribution::with('user')
            ->where('status', 'on_process')
            ->latest()
            ->get();

        return view('mwc.approval-distribution-koin-nu', compact('items'));
    }

    public function approve($id)
    {
        $item = Distribution::findOrFail($id);
        
        $item->update([
            'status' => 'validated',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('mwc.approval-distribution-koin-nu')
            ->with('success', 'Laporan Pentasarufan berhasil disetujui.');
    }

    public function reject($id)
    {
        $item = Distribution::findOrFail($id);
        
        $item->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('mwc.approval-distribution-koin-nu')
            ->with('success', 'Laporan Pentasarufan telah ditolak.');
    }
}
