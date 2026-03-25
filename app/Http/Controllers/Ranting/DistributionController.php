<?php

namespace App\Http\Controllers\Ranting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distribution;
use App\Models\Income;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DistributionController extends Controller
{
    public function index()
    {
        $items = Distribution::latest()->get();

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
            'documentation_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $totalAllowed = Income::where('user_id', Auth::id())->sum('allowed_budget');
        $totalSpent = Distribution::where('user_id', Auth::id())->sum('cost_amount');

        if (($totalSpent + $validated['cost_amount']) > $totalAllowed) {
            $remaining = max($totalAllowed - $totalSpent, 0);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Saldo tidak mencukupi. Sisa saldo yang dapat digunakan: Rp ' . number_format($remaining, 0, ',', '.')]);
        }

        DB::beginTransaction();

        try {
            $data = [
                'user_id' => Auth::id(),
                'transaction_code' => $this->generateTransactionCode(),
                'date' => $validated['date'],
                'event_name' => $validated['event_name'],
                'pilar_type' => $validated['pilar_type'],
                'cost_amount' => $validated['cost_amount'],
                'penerima_manfaat' => $validated['penerima_manfaat'],
                'status' => 'on_process',
            ];

            if ($request->hasFile('documentation_file')) {
                $data['documentation_file'] = $this->saveDocumentationFile(
                    $request->file('documentation_file')
                );
            }

            Distribution::create($data);

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
        $item = Distribution::findOrFail($id);

        if ($item->status === 'validated') {
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

        $totalAllowed = Income::where('user_id', Auth::id())->sum('allowed_budget');
        $totalSpent = Distribution::where('user_id', Auth::id())
            ->where('id', '!=', $id)
            ->sum('cost_amount');

        if (($totalSpent + $validated['cost_amount']) > $totalAllowed) {
            $remaining = max($totalAllowed - $totalSpent, 0);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Saldo tidak mencukupi. Sisa saldo yang dapat digunakan: Rp ' . number_format($remaining, 0, ',', '.')]);
        }

        DB::beginTransaction();

        try {
            $data = [
                'date' => $validated['date'],
                'event_name' => $validated['event_name'],
                'pilar_type' => $validated['pilar_type'],
                'cost_amount' => $validated['cost_amount'],
                'penerima_manfaat' => $validated['penerima_manfaat'],
            ];

            if ($request->hasFile('documentation_file')) {
                $this->deleteDocumentationFile($item->documentation_file);

                $data['documentation_file'] = $this->saveDocumentationFile(
                    $request->file('documentation_file')
                );
            }

            $item->update($data);

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
        $item = Distribution::findOrFail($id);

        if ($item->status === 'validated') {
            return redirect()
                ->route('ranting.distribution.index')
                ->withErrors(['error' => 'Data sudah divalidasi, tidak dapat dihapus.']);
        }

        $this->deleteDocumentationFile($item->documentation_file);

        $item->delete();

        return redirect()
            ->route('ranting.distribution.index')
            ->with('success', 'Data pentasarufan berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:distributions,id'],
        ]);

        $items = Distribution::whereIn('id', $request->ids)
            ->where('status', '!=', 'validated')
            ->get();

        foreach ($items as $item) {
            $this->deleteDocumentationFile($item->documentation_file);
            $item->delete();
        }

        return redirect()
            ->route('ranting.distribution.index')
            ->with('success', count($items) . ' data berhasil dihapus.');
    }

    private function saveDocumentationFile($file): string
    {
        $basePath = env('UPLOAD_PUBLIC_PATH');
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
    
        $basePath = env('UPLOAD_PUBLIC_PATH');
        $fullPath = rtrim($basePath, '/') . '/' . ltrim($path, '/');
    
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function generateTransactionCode(): string
    {
        $last = Distribution::where('transaction_code', 'like', 'DST%')->orderByDesc('id')->first();

        if (!$last || !$last->transaction_code) {
            return 'DST00001';
        }

        preg_match('/DST(\d+)/', $last->transaction_code, $matches);

        $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        $nextNumber = $lastNumber + 1;

        $digitLength = max(5, strlen((string) $nextNumber));

        return 'DST' . str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
    }
    
}