<?php

namespace App\Http\Controllers\Ranting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MunfiqData;
use App\Models\DataRanting;

class ManagementMunfiqController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = MunfiqData::where('data_ranting_id', auth()->user()->ranting_id)->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_kaleng', 'like', "%{$search}%");
            });
        }

        $munfiqs = $query->paginate(10)->withQueryString();
        return view('ranting.management-munfiq.index', compact('munfiqs', 'search'));
    }

    public function create()
    {
        return view('ranting.management-munfiq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'status' => 'required|in:Aktif,Pasif',
        ]);

        $ranting = DataRanting::findOrFail(auth()->user()->ranting_id);
        
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
        $data['data_ranting_id'] = $ranting->id;
        $data['kode_kaleng'] = $kodeKaleng;

        MunfiqData::create($data);

        return redirect()->route('ranting.management-munfiq.index')
            ->with('success', 'Data Munfiq berhasil ditambahkan');
    }

    public function edit(MunfiqData $management_munfiq)
    {
        abort_if($management_munfiq->data_ranting_id != auth()->user()->ranting_id, 403);
        $munfiq = $management_munfiq;
        return view('ranting.management-munfiq.edit', compact('munfiq'));
    }

    public function update(Request $request, MunfiqData $management_munfiq)
    {
        abort_if($management_munfiq->data_ranting_id != auth()->user()->ranting_id, 403);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'status' => 'required|in:Aktif,Pasif',
            'kode_kaleng' => 'required|string|max:255|unique:munfiq_data,kode_kaleng,' . $management_munfiq->id,
        ]);

        $management_munfiq->update([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'status' => $request->status,
            'kode_kaleng' => $request->kode_kaleng,
        ]);

        return redirect()->route('ranting.management-munfiq.index')
            ->with('success', 'Data Munfiq berhasil diupdate');
    }

    public function destroy(MunfiqData $management_munfiq)
    {
        abort_if($management_munfiq->data_ranting_id != auth()->user()->ranting_id, 403);
        $management_munfiq->delete();
        return redirect()->route('ranting.management-munfiq.index')
            ->with('success', 'Data Munfiq berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        
        if ($ids && is_array($ids)) {
            MunfiqData::whereIn('id', $ids)
                ->where('data_ranting_id', auth()->user()->ranting_id)
                ->delete();
            return redirect()->route('ranting.management-munfiq.index')
                ->with('success', count($ids) . ' data munfiq berhasil dihapus');
        }

        return redirect()->route('ranting.management-munfiq.index')
            ->with('error', 'Tidak ada data yang dipilih untuk dihapus');
    }
}
