<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Wilayah;
use App\Models\DataRanting;

class RantingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $list = [
            'KESESI',
            'SIDOMULYO 1',
            'SIOOMULYO 2',
            'KAIBAHAN',
            'PONOLAWEN',
            'SIDOSARI',
            'KRANDON',
            'PATINREJO',
            'KWIGARAN',
            'LANGENSARI',
            'SRINAHAN',
            'WATUPAYUNG',
            'KARYOMUKTI',
            'PODOSARI',
            'MULYOREJO',
        ];

        $alphabets = range('A', 'Z');
        $wilayahIds = [1, 2, 3];

        foreach ($list as $index => $nama) {
            DataRanting::updateOrCreate(
                ['nama_ranting' => $nama],
                [
                    'wilayah_id' => $wilayahIds[$index % 3],
                    'kode_ranting' => $alphabets[$index % 26],
                    'alamat' => 'Jl. ' . $nama,
                ]
            );
        }
        
    }
}
