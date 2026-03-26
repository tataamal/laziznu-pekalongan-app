<?php

namespace App\Imports;

use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WilayahImport implements ToModel, WithHeadingRow
{
    private $headersChecked = false;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (!$this->headersChecked) {
            $requiredHeaders = ['nama_wilayah', 'alamat', 'pic', 'telp_pic'];
            foreach ($requiredHeaders as $header) {
                if (!array_key_exists($header, $row)) {
                    throw new \Exception("Format header tidak sesuai template. Kolom '$header' tidak ditemukan.");
                }
            }
            $this->headersChecked = true;
        }

        if (empty($row['nama_wilayah'])) {
            return null;
        }

        return new Wilayah([
            'nama_wilayah' => $row['nama_wilayah'],
            'alamat'       => $row['alamat'] ?? null,
            'pic'          => $row['pic'] ?? null,
            'telp_pic'     => $row['telp_pic'] ?? null,
        ]);
    }
}
