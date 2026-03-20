<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\Wilayah;
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
        $filters = $request->only(['start_date', 'end_date', 'wilayah_id', 'source_type']);
        $wilayah = Wilayah::all();
        
        $distributions = collect();
        if ($request->hasAny(['start_date', 'end_date', 'wilayah_id', 'source_type'])) {
            $distributions = $this->reportService->getPentasarufanData($filters);
        }

        return view('pc.export-report', compact('wilayah', 'distributions'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'wilayah_id', 'source_type']);
        
        return Excel::download(
            new PentasarufanExport($filters), 
            'Laporan-Pentasarufan-' . now()->format('YmdHis') . '.xlsx'
        );
    }
}
