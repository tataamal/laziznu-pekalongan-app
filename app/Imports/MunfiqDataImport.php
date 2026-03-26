<?php

namespace App\Imports;

use App\Models\MunfiqData;
use App\Models\DataRanting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class MunfiqDataImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("File excel kosong.");
        }

        $firstRow = $rows->first()->toArray();
        $requiredHeaders = ['ranting', 'nama', 'jenis_kelamin', 'alamat', 'status'];
        foreach ($requiredHeaders as $header) {
            if (!array_key_exists($header, $firstRow)) {
                throw new \Exception("Format header tidak sesuai template. Kolom '$header' tidak ditemukan.");
            }
        }
        // Cache ranting data by UPPERCASE name for case-insensitive matching
        $rantings = DataRanting::all()->keyBy(function($item) {
            return strtoupper(trim($item->nama));
        });

        foreach ($rows as $row) {
            // Check required fields based on excel template columns
            if (!isset($row['ranting']) || !isset($row['nama'])) {
                continue;
            }
            
            if (strtoupper(trim($row['ranting'])) === 'INFO') {
                continue; // Skip the info template row
            }

            $rantingName = strtoupper(trim($row['ranting']));
            $ranting = $rantings->get($rantingName);

            if (!$ranting) {
                throw new \Exception("Ranting tidak ditemukan untuk '" . $row['ranting'] . "'. Pastikan nama ranting yang diinput sesuai dengan data di sistem.");
            }

            $jenisKelamin = strtoupper(trim($row['jenis_kelamin'] ?? 'L'));
            if (!in_array($jenisKelamin, ['L', 'P'])) {
                $jenisKelamin = 'L';
            }

            $alamat = trim($row['alamat'] ?? '');
            if (empty($alamat)) {
                $alamat = '-';
            }

            $statusStr = ucwords(strtolower(trim($row['status'] ?? 'Aktif')));
            if (!in_array($statusStr, ['Aktif', 'Pasif'])) {
                $statusStr = 'Aktif';
            }

            // Generate kode_kaleng logic
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

            MunfiqData::create([
                'data_ranting_id' => $ranting->id,
                'nama' => trim($row['nama']),
                'jenis_kelamin' => $jenisKelamin,
                'alamat' => $alamat,
                'status' => $statusStr,
                'kode_kaleng' => $kodeKaleng,
            ]);
        }
    }
}
