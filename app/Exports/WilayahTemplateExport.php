<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class WilayahTemplateExport implements WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'nama_wilayah',
            'alamat',
            'pic',
            'no_telp'
        ];
    }

    public function title(): string
    {
        return 'Template Import Wilayah';
    }
}
