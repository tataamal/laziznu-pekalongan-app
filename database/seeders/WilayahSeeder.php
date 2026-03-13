<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wilayah;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $list = [
            'UPZIS MWCNU Kedungwuni',
            'UPZIS MWCNU Wonopringgo',
            'UPZIS MWCNU Wiradesa',
            'UPZIS MWCNU Kajen',
            'UPZIS MWCNU Bojong',
            'UPZIS MWCNU Buaran',
            'UPZIS MWCNU Tirto',
            'UPZIS MWCNU Sragi',
            'UPZIS MWCNU Kesesi',
            'UPZIS MWCNU Doro',
            'UPZIS MWCNU Karanganyar',
            'UPZIS MWCNU Paninggaran',
            'UPZIS MWCNU Talun',
            'UPZIS MWCNU Lebakbarang',
            'UPZIS MWCNU Petungkriyono',
            'UPZIS MWCNU Kandangserang',
            'UPZIS MWCNU Siwalan',
            'UPZIS MWCNU Karangdadap',
            'UPZIS MWCNU Wonokerto',
        ];

        foreach ($list as $nama) {
            Wilayah::create([
                'nama_wilayah' => $nama,
                'alamat' => null,
                'pic' => null,
                'telp_pic' => null,
            ]);
        }
    }
}