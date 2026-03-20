<?php

namespace App\Exports;

use App\Services\ReportService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PentasarufanExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $filters;
    protected $reportService;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->reportService = app(ReportService::class);
    }

    public function view(): View
    {
        $distributions = $this->reportService->getPentasarufanData($this->filters);
        
        return view('exports.pentasarufan', [
            'distributions' => $distributions,
            'filters' => $this->filters
        ]);
    }

    public function title(): string
    {
        return 'Laporan Pentasarufan';
    }
}
