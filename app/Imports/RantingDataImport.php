<?php

namespace App\Imports;

use App\Models\DataRanting;
use App\Models\Wilayah;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class RantingDataImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("File excel kosong.");
        }

        $firstRow = $rows->first()->toArray();
        $requiredHeaders = ['wilayah', 'nama_ranting', 'alamat'];
        foreach ($requiredHeaders as $header) {
            if (!array_key_exists($header, $firstRow)) {
                throw new \Exception("Format header tidak sesuai template. Kolom '$header' tidak ditemukan.");
            }
        }
        // Cache wilayah data by UPPERCASE name for case-insensitive matching
        $wilayahs = Wilayah::all()->keyBy(function($item) {
            return strtoupper(trim($item->nama_wilayah));
        });

        foreach ($rows as $row) {
            // Check required fields based on excel template columns
            if (!isset($row['wilayah']) || !isset($row['nama_ranting'])) {
                continue;
            }
            
            if (strtoupper(trim($row['wilayah'])) === 'INFO') {
                continue; // Skip the info template row
            }

            $wilayahName = strtoupper(trim($row['wilayah']));
            $wilayah = $wilayahs->get($wilayahName);

            if (!$wilayah) {
                throw new \Exception("Wilayah tidak ditemukan untuk '" . $row['wilayah'] . "'. Pastikan nama wilayah yang diinput sesuai dengan data di sistem.");
            }

            $namaRanting = trim($row['nama_ranting']);
            if (empty($namaRanting)) {
                continue;
            }

            $alamat = trim($row['alamat'] ?? '');
            
            // Check if Ranting with same name already exists in this Wilayah
            $existingRanting = DataRanting::where('wilayah_id', $wilayah->id)
                                          ->where('nama', $namaRanting)
                                          ->first();
                                          
            if ($existingRanting) {
                Log::info("Import Ranting: Ranting '$namaRanting' sudah ada di wilayah {$wilayah->nama_wilayah}, di-skip.");
                continue;
            }

            // Generate kode_ranting logic
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

            DataRanting::create([
                'wilayah_id' => $wilayah->id,
                'nama' => $namaRanting,
                'alamat' => empty($alamat) ? null : $alamat,
                'kode_ranting' => $nextKode,
            ]);
        }
    }
}
