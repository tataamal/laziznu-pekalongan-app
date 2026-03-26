<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataRanting;
use App\Models\Wilayah;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RantingTemplateExport;
use App\Imports\RantingDataImport;

class ManagementRantingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = DataRanting::with('wilayah')->orderBy('id');

        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_ranting', 'like', "%{$search}%")
                  ->orWhereHas('wilayah', function ($q) use ($search) {
                      $q->where('nama_wilayah', 'like', "%{$search}%");
                  });
        }

        $dataRantings = $query->paginate(10)->withQueryString();
        
        return view('developer.management-ranting.index', compact('dataRantings', 'search'));
    }

    public function create()
    {
        $wilayahs = Wilayah::all();
        return view('developer.management-ranting.create', compact('wilayahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayah,id',
            'nama' => 'required|string|max:255',
        ]);

        $lastRanting = DataRanting::orderBy('id', 'desc')->first();
        $nextKode = 'A';

        if ($lastRanting && $lastRanting->kode_ranting) {
            $nextKode = $lastRanting->kode_ranting;
            $nextKode++; 
            
            // pastikan unik
            while(DataRanting::where('kode_ranting', $nextKode)->exists()) {
                $nextKode++;
            }
        }

        $data = $request->all();
        $data['kode_ranting'] = $nextKode;

        DataRanting::create($data);

        return redirect()->route('developer.management-ranting.index')
            ->with('success', 'Data ranting berhasil ditambahkan');
    }

    public function edit(DataRanting $management_ranting)
    {
        $dataRanting = $management_ranting;
        $wilayahs = Wilayah::all();
        return view('developer.management-ranting.edit', compact('dataRanting', 'wilayahs'));
    }

    public function update(Request $request, DataRanting $management_ranting)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayah,id',
            'nama' => 'required|string|max:255',
            'kode_ranting' => 'required|string|max:255|unique:data_ranting,kode_ranting,' . $management_ranting->id,
        ]);

        $management_ranting->update($request->all());

        return redirect()->route('developer.management-ranting.index')
            ->with('success', 'Data ranting berhasil diupdate');
    }

    public function destroy(DataRanting $management_ranting)
    {
        $management_ranting->delete();

        return redirect()->route('developer.management-ranting.index')
            ->with('success', 'Data ranting berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        
        if ($ids && is_array($ids)) {
            DataRanting::whereIn('id', $ids)->delete();
            return redirect()->route('developer.management-ranting.index')
                ->with('success', count($ids) . ' data ranting berhasil dihapus');
        }

        return redirect()->route('developer.management-ranting.index')
            ->with('error', 'Tidak ada data yang dipilih untuk dihapus');
    }

    public function template()
    {
        return Excel::download(new RantingTemplateExport, 'template-data-ranting.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new RantingDataImport, $request->file('file'));
            return redirect()->route('developer.management-ranting.index')
                ->with('success', 'Data ranting berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->route('developer.management-ranting.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}
