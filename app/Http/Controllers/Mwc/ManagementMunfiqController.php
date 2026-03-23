<?php

namespace App\Http\Controllers\Mwc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MunfiqData;
use App\Models\DataRanting;

class ManagementMunfiqController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = MunfiqData::with('data_ranting')
            ->whereHas('data_ranting', function ($q) {
                $q->where('wilayah_id', auth()->user()->wilayah_id);
            })->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_kaleng', 'like', "%{$search}%")
                  ->orWhereHas('data_ranting', function ($qr) use ($search) {
                      $qr->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $munfiqs = $query->paginate(10)->withQueryString();
        return view('mwc.management-munfiq.index', compact('munfiqs', 'search'));
    }

    public function create()
    {
        $rantings = DataRanting::where('wilayah_id', auth()->user()->wilayah_id)->orderBy('nama')->get();
        return view('mwc.management-munfiq.create', compact('rantings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_ranting_id' => 'required|exists:data_ranting,id',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'status' => 'required|in:Aktif,Pasif',
        ]);

        $ranting = DataRanting::where('wilayah_id', auth()->user()->wilayah_id)->findOrFail($request->data_ranting_id);
        
        $lastMunfiq = MunfiqData::where('data_ranting_id', $ranting->id)
            ->orderBy('id', 'desc')
            ->first();

        $urutan = 1;
        if ($lastMunfiq && $lastMunfiq->kode_kaleng) {
            $parts = explode('-', $lastMunfiq->kode_kaleng);
            if(count($parts) === 2) {
                $urutan = intval($parts[1]) + 1;
            } else {
                $urutan = MunfiqData::where('data_ranting_id', $ranting->id)->count() + 1;
            }
        }
        
        $kodeKaleng = $ranting->kode_ranting . '-' . str_pad($urutan, 4, '0', STR_PAD_LEFT);
        while(MunfiqData::where('kode_kaleng', $kodeKaleng)->exists()){
            $urutan++;
            $kodeKaleng = $ranting->kode_ranting . '-' . str_pad($urutan, 4, '0', STR_PAD_LEFT);
        }

        $data = $request->all();
        $data['kode_kaleng'] = $kodeKaleng;

        MunfiqData::create($data);

        return redirect()->route('mwc.management-munfiq.index')
            ->with('success', 'Data Munfiq berhasil ditambahkan');
    }

    public function edit(MunfiqData $management_munfiq)
    {
        abort_if($management_munfiq->data_ranting->wilayah_id != auth()->user()->wilayah_id, 403);
        $munfiq = $management_munfiq;
        $rantings = DataRanting::where('wilayah_id', auth()->user()->wilayah_id)->orderBy('nama')->get();
        return view('mwc.management-munfiq.edit', compact('munfiq', 'rantings'));
    }

    public function update(Request $request, MunfiqData $management_munfiq)
    {
        abort_if($management_munfiq->data_ranting->wilayah_id != auth()->user()->wilayah_id, 403);
        
        $request->validate([
            'data_ranting_id' => 'required|exists:data_ranting,id',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'status' => 'required|in:Aktif,Pasif',
            'kode_kaleng' => 'required|string|max:255|unique:munfiq_data,kode_kaleng,' . $management_munfiq->id,
        ]);

        $ranting = DataRanting::where('wilayah_id', auth()->user()->wilayah_id)->findOrFail($request->data_ranting_id);

        $management_munfiq->update($request->all());

        return redirect()->route('mwc.management-munfiq.index')
            ->with('success', 'Data Munfiq berhasil diupdate');
    }

    public function destroy(MunfiqData $management_munfiq)
    {
        abort_if($management_munfiq->data_ranting->wilayah_id != auth()->user()->wilayah_id, 403);
        $management_munfiq->delete();
        return redirect()->route('mwc.management-munfiq.index')
            ->with('success', 'Data Munfiq berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        
        if ($ids && is_array($ids)) {
            MunfiqData::whereIn('id', $ids)
                ->whereHas('data_ranting', function ($q) {
                    $q->where('wilayah_id', auth()->user()->wilayah_id);
                })
                ->delete();
            return redirect()->route('mwc.management-munfiq.index')
                ->with('success', count($ids) . ' data munfiq berhasil dihapus');
        }

        return redirect()->route('mwc.management-munfiq.index')
            ->with('error', 'Tidak ada data yang dipilih untuk dihapus');
    }
}
