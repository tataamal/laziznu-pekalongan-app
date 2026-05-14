<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\KoinNuTransactionRepository;
use App\Repositories\KoinNuDistributionRepository;
use App\Models\KoinNuTransaction;

use Illuminate\Support\Facades\Auth;

class ApprovalIncomeKoinNU extends Controller
{
    public function __construct(
        protected KoinNuTransactionRepository $transactionRepo,
        protected KoinNuDistributionRepository $distributionRepo,
    ) {
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $wilayahId = $user->wilayah_id;
        
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
            'status' => $request->get('status'),
        ];
        
        $status = $request->get('status', 'all');
        
        if ($status == 'pending') {
            $requestApproval = $this->transactionRepo->getRequestApprovalKoinNuMwc($wilayahId, $startDate, $endDate, $filters);
            $historyApproval = collect();
        } elseif ($status == 'validated' || $status == 'rejected') {
            $requestApproval = collect();
            $historyApproval = $this->transactionRepo->getHistoryKoinNuMwc($wilayahId, $startDate, $endDate, $filters);
        } else {
            $requestApproval = $this->transactionRepo->getRequestApprovalKoinNuMwc($wilayahId, $startDate, $endDate, $filters);
            $historyApproval = $this->transactionRepo->getHistoryKoinNuMwc($wilayahId, $startDate, $endDate, $filters);
        }
        
        $rantingName = $requestApproval->first()?->ranting?->nama_ranting ?? '-';
        
        return view('mwc.approval-income-koin-nu', compact(
            'requestApproval',
            'historyApproval',
            'rantingName'
        ));
    }

    public function approve($id)
    {
        $item = KoinNuTransaction::findOrFail($id);

        $item->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('mwc.approval-income-koin-nu')
            ->with('success', 'Laporan Pemasukan berhasil disetujui.');
    }

    public function reject($id)
    {
        $item = KoinNuTransaction::findOrFail($id);

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
