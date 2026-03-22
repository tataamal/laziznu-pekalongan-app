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
            DataRanting::create([
                'wilayah_id' => $wilayahIds[$index % 3],
                'nama' => $nama,
                'kode_ranting' => $alphabets[$index % 26],
            ]);
        }
        
    }
}
