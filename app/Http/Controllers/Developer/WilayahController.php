<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Imports\WilayahImport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = Wilayah::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_wilayah', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%")
                  ->orWhere('telp_pic', 'like', "%{$search}%");
        }
        
        $wilayahs = $query->latest()->paginate(10)->withQueryString();
        
        return view('developer.wilayah.index', compact('wilayahs', 'request'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_wilayah' => 'required|string|max:255|unique:wilayah,nama_wilayah',
            'alamat'       => 'nullable|string',
            'pic'          => 'nullable|string|max:255',
            'telp_pic'     => 'nullable|string|max:20',
        ]);

        $wilayah = Wilayah::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_WILAYAH',
            'description' => 'Menambahkan wilayah baru: ' . $wilayah->nama_wilayah,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('developer.wilayah.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, Wilayah $wilayah)
    {
        $validated = $request->validate([
            'nama_wilayah' => 'required|string|max:255|unique:wilayah,nama_wilayah,' . $wilayah->id,
            'alamat'       => 'nullable|string',
            'pic'          => 'nullable|string|max:255',
            'telp_pic'     => 'nullable|string|max:20',
        ]);

        $wilayah->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_WILAYAH',
            'description' => 'Memperbarui wilayah: ' . $wilayah->nama_wilayah,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('developer.wilayah.index')->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Wilayah $wilayah)
    {
        // Check if there are users in this wilayah before deleting
        if ($wilayah->users()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus wilayah karena masih ada ' . $wilayah->users()->count() . ' user yang terhubung.');
        }

        $namaWilayah = $wilayah->nama_wilayah;
        $wilayah->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_WILAYAH',
            'description' => 'Menghapus wilayah: ' . $namaWilayah,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->route('developer.wilayah.index')->with('success', 'Wilayah berhasil dihapus.');
    }

    /**
     * Delete multiple wilayah.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'wilayah_ids' => 'required|array',
            'wilayah_ids.*' => 'exists:wilayah,id',
        ]);

        $wilayahs = Wilayah::whereIn('id', $request->wilayah_ids)->get();
        $deletedCount = 0;

        foreach ($wilayahs as $wilayah) {
            // Check if there are users in this wilayah before deleting
            if (!$wilayah->users()->exists()) {
                $wilayah->delete();
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'BULK_DELETE_WILAYAH',
                'description' => 'Menghapus ' . $deletedCount . ' data wilayah.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        if ($deletedCount == 0 && count($request->wilayah_ids) > 0) {
            return redirect()->route('developer.wilayah.index')->with('error', 'Tidak ada wilayah yang dapat dihapus karena semua wilayah yang dipilih masih memiliki user yang terhubung.');
        } elseif ($deletedCount < count($request->wilayah_ids)) {
            return redirect()->route('developer.wilayah.index')->with('success', $deletedCount . ' wilayah berhasil dihapus. Beberapa wilayah tidak dapat dihapus karena masih memiliki user yang terhubung.');
        }

        return redirect()->route('developer.wilayah.index')->with('success', $deletedCount . ' wilayah berhasil dihapus.');
    }

    /**
     * Import wilayah from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new WilayahImport, $request->file('file'));

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'IMPORT_WILAYAH',
                'description' => 'Mengimport data wilayah dari Excel',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->route('developer.wilayah.index')->with('success', 'Data wilayah berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('developer.wilayah.index')->with('error', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $path = public_path('templates/template_import_wilayah.xlsx');
        
        if (!file_exists($path)) {
            abort(404, 'Template file not found.');
        }

        return response()->download($path);
    }
}
