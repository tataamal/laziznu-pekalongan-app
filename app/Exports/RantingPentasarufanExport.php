<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RantingPentasarufanExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $distributions;
    protected $filters;

    public function __construct(Collection $distributions, array $filters)
    {
        $this->distributions = $distributions;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('exports.pentasarufan', [
            'distributions' => $this->distributions,
            'filters' => $this->filters
        ]);
    }

    public function title(): string
    {
        return 'Laporan Pentasarufan Ranting';
    }
}
