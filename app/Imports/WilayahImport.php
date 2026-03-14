<?php

namespace App\Imports;

use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WilayahImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
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
