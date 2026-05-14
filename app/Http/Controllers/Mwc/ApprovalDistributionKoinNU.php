<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KoinNuDistribution;
use Illuminate\Support\Facades\Auth;

class ApprovalDistributionKoinNU extends Controller
{
    public function index(Request $request)
    {
        $wilayahId = Auth::user()->wilayah_id;
        
        $startDate = null;
        $endDate = null;
        
        if ($request->has('date_range') && $request->date_range != '') {
            $dates = explode(' to ', $request->date_range);
            $startDate = $dates[0] ?? null;
            $endDate = $dates[1] ?? $dates[0] ?? null;
        }

        $filters = [
            'transaction_code' => $request->get('transaction_code'),
            'ranting_name' => $request->get('ranting_name'),
            'status' => $request->get('status', 'all'),
        ];

        $buildQuery = function($queryStatus) use ($wilayahId, $startDate, $endDate, $filters) {
            $query = KoinNuDistribution::with('user')
                ->whereHas('user', function($q) use ($wilayahId, $filters) {
                    $q->where('wilayah_id', $wilayahId);
                    if (!empty($filters['ranting_name'])) {
                        $q->where('name', 'like', '%' . $filters['ranting_name'] . '%');
                    }
                });

            if (!empty($filters['transaction_code'])) {
                $query->where('transaction_code', 'like', '%' . $filters['transaction_code'] . '%');
            }

            if ($startDate) {
                $query->where('date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('date', '<=', $endDate);
            }

            if (is_array($queryStatus)) {
                if ($filters['status'] !== 'all') {
                    if ($filters['status'] === 'approved') {
                        $query->whereIn('status', ['validated', 'approved']);
                    } else {
                        $query->where('status', $filters['status']);
                    }
                } else {
                    $query->whereIn('status', $queryStatus);
                }
            } else {
                $query->where('status', $queryStatus);
            }

            return $query->latest();
        };

        $status = $filters['status'];
        
        if ($status == 'on_process') {
            $items = $buildQuery('on_process')->get();
            $historyItems = collect();
        } elseif ($status == 'approved' || $status == 'rejected' || $status == 'validated') {
            $items = collect();
            $historyItems = $buildQuery(['validated', 'rejected', 'approved'])->limit(50)->get();
        } else {
            $items = $buildQuery('on_process')->get();
            $historyItems = $buildQuery(['validated', 'rejected', 'approved'])->limit(50)->get();
        }

        return view('mwc.approval-distribution-koin-nu', compact('items', 'historyItems'));
    }

    public function approve($id)
    {
        $item = KoinNuDistribution::findOrFail($id);
        
        $item->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('mwc.approval-distribution-koin-nu')
            ->with('success', 'Laporan Pentasarufan berhasil disetujui.');
    }

    public function reject($id)
    {
        $item = KoinNuDistribution::findOrFail($id);
        
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
