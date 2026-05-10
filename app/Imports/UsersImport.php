<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Wilayah;
use App\Models\DataRanting;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    private $headersChecked = false;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        \Log::info('Import row content: ' . json_encode($row));

        // Skip rows that do not have a name (e.g. from reference sheets or empty rows)
        if (empty($row['name'])) {
            return null;
        }

        if (!$this->headersChecked) {
            $requiredHeaders = ['name', 'email', 'password', 'role'];
            foreach ($requiredHeaders as $header) {
                if (!array_key_exists($header, $row)) {
                    throw new \Exception("Format header tidak sesuai template. Kolom '$header' tidak ditemukan.");
                }
            }
            $this->headersChecked = true;
        }

        // Lookup Wilayah ID by Name
        $wilayahId = null;
        if (!empty($row['wilayah'])) {
            $wilayah = Wilayah::where('nama_wilayah', $row['wilayah'])->first();
            $wilayahId = $wilayah ? $wilayah->id : null;
        }

        // Lookup Ranting ID by Name
        $rantingId = null;
        if (!empty($row['ranting'])) {
            $ranting = DataRanting::where('nama_ranting', $row['ranting'])->first();
            $rantingId = $ranting ? $ranting->id : null;
        }

        return new User([
            'name'       => $row['name'],
            'email'      => $row['email'],
            'password'   => Hash::make($row['password']),
            'role'       => $row['role'],
            'no_telp'    => $row['no_telp'] ?? $row['telpon'] ?? null,
            'wilayah_id' => $wilayahId,
            'ranting_id' => $rantingId,
        ]);
    }
}
