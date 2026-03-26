<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RantingTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Wilayah',
            'Nama Ranting',
            'Alamat'
        ];
    }

    public function array(): array
    {
        return [
            ['INFO', 'Isi Wilayah harus sama persis (tidak case sensitive) dengan nama wilayah di sistem', ''],
            ['KEDUNGWUNI', 'Ranting Kedungwuni Timur', 'Jl. Pahlawan No. 1'],
            ['KEDUNGWUNI', 'Ranting Kedungwuni Barat', 'Jl. Pahlawan No. 2'],
        ];
    }
}
