<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Exports\PentasarufanExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        // For MWC, we only allow filtering by date and source type.
        // Wilayah is strictly bound to the authenticated user's wilayah.
        $filters = $request->only(['start_date', 'end_date', 'source_type']);
        $filters['wilayah_id'] = auth()->user()->wilayah_id;
        
        $distributions = collect();
        if ($request->hasAny(['start_date', 'end_date', 'source_type'])) {
            $distributions = $this->reportService->getPentasarufanData($filters);
        }

        return view('mwc.export-report', compact('distributions'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'source_type']);
        $filters['wilayah_id'] = auth()->user()->wilayah_id;
        
        return Excel::download(
            new PentasarufanExport($filters), 
            'Laporan-Pentasarufan-MWC-' . now()->format('YmdHis') . '.xlsx'
        );
    }
}
