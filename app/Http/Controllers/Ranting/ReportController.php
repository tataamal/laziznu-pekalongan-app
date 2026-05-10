<?php

namespace App\Http\Controllers\Ranting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distribution;
use App\Exports\RantingPentasarufanExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date']);
        
        $distributions = collect();
        if ($request->hasAny(['start_date', 'end_date'])) {
            $query = \App\Models\KoinNuDistribution::with('wilayah')->where('ranting_id', auth()->user()->ranting_id);
            
            if (!empty($filters['start_date'])) {
                $query->where('date', '>=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $query->where('date', '<=', $filters['end_date']);
            }
            
            // Format to match the existing export view structure
            $distributions = $query->orderBy('date', 'desc')->get()->map(function ($item) {
                return [
                    'date' => $item->date,
                    'transaction_code' => $item->distribution_code,
                    'penerima_manfaat' => $item->jumlah_penerima_manfaat_ranting,
                    'event_name' => $item->deskripsi,
                    'amount' => $item->jumlah_pentasarufan_ranting,
                    'type' => 'Koin NU',
                    'wilayah' => auth()->user()->wilayah->nama_wilayah ?? '-',
                    'status' => $item->status,
                ];
            });
        }

        return view('ranting.export-report', compact('distributions'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date']);
        
        // Use the same logic to fetch the distributions
        $query = \App\Models\KoinNuDistribution::with('wilayah')->where('ranting_id', auth()->user()->ranting_id);
            
        if (!empty($filters['start_date'])) {
            $query->where('date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->where('date', '<=', $filters['end_date']);
        }
        
        $distributions = $query->orderBy('date', 'desc')->get()->map(function ($item) {
            return [
                'date' => $item->date,
                'transaction_code' => $item->distribution_code,
                'penerima_manfaat' => $item->jumlah_penerima_manfaat_ranting,
                'event_name' => $item->deskripsi,
                'amount' => $item->jumlah_pentasarufan_ranting,
                'type' => 'Koin NU',
                'wilayah' => auth()->user()->wilayah->nama_wilayah ?? '-',
                'status' => $item->status,
            ];
        });

        return Excel::download(
            new RantingPentasarufanExport($distributions, $filters), 
            'Laporan-Pentasarufan-Ranting-' . now()->format('YmdHis') . '.xlsx'
        );
    }
}
