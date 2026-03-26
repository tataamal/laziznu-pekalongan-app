<?php

namespace App\Imports;

use App\Models\User;
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
        if (!$this->headersChecked) {
            $requiredHeaders = ['nama', 'email', 'password', 'role', 'telpon'];
            foreach ($requiredHeaders as $header) {
                if (!array_key_exists($header, $row)) {
                    throw new \Exception("Format header tidak sesuai template. Kolom '$header' tidak ditemukan.");
                }
            }
            $this->headersChecked = true;
        }
        return new User([
            'name'       => $row['nama'],
            'email'      => $row['email'],
            'password'   => Hash::make($row['password']),
            'role'       => $row['role'],
            'telpon'     => $row['telpon'] ?? null,
        ]);
    }
}
