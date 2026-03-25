<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MunfiqTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Ranting',
            'Nama',
            'Jenis Kelamin',
            'Alamat',
            'Status'
        ];
    }

    public function array(): array
    {
        return [
            ['INFO', 'Isi Ranting harus sama (tidak case sensitive) dengan nama ranting di sistem', '', '', ''],
            ['KRAPYAK', 'Budi Santoso', 'L', 'Jl. Merdeka No 1', 'Aktif'],
            ['TIRTO', 'Siti Aminah', 'P', 'Jl. Kemerdekaan No 2', 'Aktif'],
        ];
    }
}
