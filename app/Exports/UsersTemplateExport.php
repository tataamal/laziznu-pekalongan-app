<?php

namespace App\Exports;

use App\Models\Wilayah;
use App\Models\DataRanting;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class UsersTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new UsersTemplateFormSheet(),
            new UsersTemplateDataSheet(),
        ];
    }
}

class UsersTemplateFormSheet implements WithHeadings, WithTitle, WithEvents
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'password',
            'role',
            'no_telp',
            'wilayah',
            'ranting'
        ];
    }

    public function title(): string
    {
        return 'Template Isian';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Data Validation for Role (Dropdown statis)
                $validationRole = $sheet->getCell('D2')->getDataValidation();
                $validationRole->setType(DataValidation::TYPE_LIST);
                $validationRole->setFormula1('"pc,mwc,ranting,developer"');
                $validationRole->setShowDropDown(true);
                
                for ($i = 3; $i <= 100; $i++) {
                    $sheet->getCell("D$i")->setDataValidation(clone $validationRole);
                }

                // Data Validation for Wilayah (Mengambil dari Sheet 'Data Referensi')
                $validationWilayah = $sheet->getCell('F2')->getDataValidation();
                $validationWilayah->setType(DataValidation::TYPE_LIST);
                // Rumus merujuk ke Sheet 'Data Referensi' Kolom A (Wilayah)
                $validationWilayah->setFormula1('\'Data Referensi\'!$A$2:$A$100');
                $validationWilayah->setShowDropDown(true);
                
                for ($i = 3; $i <= 100; $i++) {
                    $sheet->getCell("F$i")->setDataValidation(clone $validationWilayah);
                }

                // Data Validation for Ranting (Mengambil dari Sheet 'Data Referensi')
                $validationRanting = $sheet->getCell('G2')->getDataValidation();
                $validationRanting->setType(DataValidation::TYPE_LIST);
                // Rumus merujuk ke Sheet 'Data Referensi' Kolom B (Ranting)
                $validationRanting->setFormula1('\'Data Referensi\'!$B$2:$B$500');
                $validationRanting->setShowDropDown(true);
                
                for ($i = 3; $i <= 100; $i++) {
                    $sheet->getCell("G$i")->setDataValidation(clone $validationRanting);
                }
            }
        ];
    }
}

class UsersTemplateDataSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        $wilayahs = Wilayah::pluck('nama_wilayah')->toArray();
        $rantings = DataRanting::pluck('nama_ranting')->toArray();

        $max = max(count($wilayahs), count($rantings));
        $data = [];

        for ($i = 0; $i < $max; $i++) {
            $data[] = [
                'wilayah' => $wilayahs[$i] ?? '',
                'ranting' => $rantings[$i] ?? '',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Daftar Wilayah',
            'Daftar Ranting'
        ];
    }

    public function title(): string
    {
        return 'Data Referensi';
    }
}
