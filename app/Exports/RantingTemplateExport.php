<?php

namespace App\Exports;

use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class RantingTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new RantingTemplateFormSheet(),
            new RantingTemplateDataSheet(),
        ];
    }
}

class RantingTemplateFormSheet implements WithHeadings, WithTitle, WithEvents
{
    public function headings(): array
    {
        return [
            'wilayah',
            'nama_ranting',
            'alamat'
        ];
    }

    public function title(): string
    {
        return 'Template Isian Ranting';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Data Validation for Wilayah (Mengambil dari Sheet 'Data Referensi')
                $validationWilayah = $sheet->getCell('A2')->getDataValidation();
                $validationWilayah->setType(DataValidation::TYPE_LIST);
                $validationWilayah->setFormula1('\'Data Referensi\'!$A$2:$A$100');
                $validationWilayah->setShowDropDown(true);
                
                for ($i = 3; $i <= 100; $i++) {
                    $sheet->getCell("A$i")->setDataValidation(clone $validationWilayah);
                }
            }
        ];
    }
}

class RantingTemplateDataSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        $wilayahs = Wilayah::pluck('nama_wilayah')->toArray();

        $data = [];
        foreach ($wilayahs as $w) {
            $data[] = [
                'wilayah' => $w
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Daftar Wilayah'
        ];
    }

    public function title(): string
    {
        return 'Data Referensi';
    }
}
