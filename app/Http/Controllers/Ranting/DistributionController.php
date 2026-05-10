<?php

namespace App\Http\Controllers\Ranting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KoinNuDistributionService;
use App\Repositories\KoinNuDistributionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DistributionController extends Controller
{
    protected KoinNuDistributionService $service;
    protected KoinNuDistributionRepository $repository;

    public function __construct(KoinNuDistributionService $service, KoinNuDistributionRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        $rantingId = Auth::user()->ranting_id;
        $items = $this->repository->getDistributionsRanting($rantingId);

        return view('ranting.distribution.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'event_name' => ['required', 'string', 'max:255'],
            'pilar_type' => ['required', 'string'],
            'cost_amount' => ['required', 'integer', 'min:0'],
            'penerima_manfaat' => ['required', 'integer', 'min:0'],
            'documentation_file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'date' => $validated['date'],
                'deskripsi' => $validated['event_name'],
                'jenis_pilar' => $validated['pilar_type'],
                'jumlah_pentasarufan_ranting' => $validated['cost_amount'],
                'jumlah_pentasarufan_mwc' => 0,
                'jumlah_pentasarufan_pc' => 0,
                'jumlah_penerima_manfaat_ranting' => $validated['penerima_manfaat'],
                'jumlah_penerima_manfaat_mwc' => 0,
                'jumlah_penerima_manfaat_pc' => 0,
            ];

            if ($request->hasFile('documentation_file')) {
                $data['file_dokumentasi'] = $this->saveDocumentationFile(
                    $request->file('documentation_file')
                );
            }

            $rantingId = Auth::user()->ranting_id;
            $this->service->createDistribution($data, $rantingId);

            DB::commit();

            return redirect()
                ->route('ranting.distribution.index')
                ->with('success', 'Data pentasarufan berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $th->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $item = $this->repository->findById($id);

        if ($item->status === 'approved') {
            return redirect()
                ->route('ranting.distribution.index')
                ->withErrors(['error' => 'Data sudah divalidasi, tidak dapat diubah.']);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'event_name' => ['required', 'string', 'max:255'],
            'pilar_type' => ['required', 'string'],
            'cost_amount' => ['required', 'integer', 'min:0'],
            'penerima_manfaat' => ['required', 'integer', 'min:0'],
            'documentation_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'date' => $validated['date'],
                'deskripsi' => $validated['event_name'],
                'jenis_pilar' => $validated['pilar_type'],
                'jumlah_pentasarufan_ranting' => $validated['cost_amount'],
                'jumlah_penerima_manfaat_ranting' => $validated['penerima_manfaat'],
            ];

            if ($request->hasFile('documentation_file')) {
                $this->deleteDocumentationFile($item->file_dokumentasi);

                $data['file_dokumentasi'] = $this->saveDocumentationFile(
                    $request->file('documentation_file')
                );
            }

            $rantingId = Auth::user()->ranting_id;
            $this->service->updateDistribution($id, $data, $rantingId);

            DB::commit();

            return redirect()
                ->route('ranting.distribution.index')
                ->with('success', 'Data pentasarufan berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengupdate data: ' . $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $item = $this->repository->findById($id);

        if ($item->status === 'approved') {
            return redirect()
                ->route('ranting.distribution.index')
                ->withErrors(['error' => 'Data sudah divalidasi, tidak dapat dihapus.']);
        }

        $this->deleteDocumentationFile($item->file_dokumentasi);

        $this->service->deleteDistribution($id);

        return redirect()
            ->route('ranting.distribution.index')
            ->with('success', 'Data pentasarufan berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:koin_nu_distributions,id'],
        ]);

        $deletedCount = 0;
        foreach ($request->ids as $id) {
            $item = $this->repository->findById($id);
            if ($item && $item->status !== 'approved') {
                $this->deleteDocumentationFile($item->file_dokumentasi);
                $this->service->deleteDistribution($id);
                $deletedCount++;
            }
        }

        return redirect()
            ->route('ranting.distribution.index')
            ->with('success', $deletedCount . ' data berhasil dihapus.');
    }

    private function saveDocumentationFile($file): string
    {
        $basePath = env('UPLOAD_PUBLIC_PATH', public_path());
        $destination = rtrim($basePath, '/') . '/distributions';
    
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
    
        $image = @imagecreatefromstring(file_get_contents($file));
    
        if ($image !== false) {
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($file->getPathname());
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $image = imagerotate($image, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($image, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($image, 90, 0);
                            break;
                    }
                }
            }
    
            $filename = uniqid() . '_' . time() . '.webp';
            $fullPath = $destination . '/' . $filename;
    
            imagewebp($image, $fullPath, 80);
            imagedestroy($image);
    
            return 'distributions/' . $filename;
        }
    
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $file->move($destination, $filename);
    
        return 'distributions/' . $filename;
    }

    private function deleteDocumentationFile(?string $path): void
    {
        if (!$path) {
            return;
        }
    
        $basePath = env('UPLOAD_PUBLIC_PATH', public_path());
        $fullPath = rtrim($basePath, '/') . '/' . ltrim($path, '/');
    
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}