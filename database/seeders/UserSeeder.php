<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wilayah;
use App\Models\DataRanting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'developer@example.com'],
            [
                'name' => 'Developer',
                'password' => Hash::make('developer123'),
                'role' => 'developer',
                'no_telp' => '081234567890',
                'wilayah_id' => null,
                'ranting_id' => null,
            ]
        );

        $wilayahMwc = Wilayah::first();
        $rantingMwc = DataRanting::first();

        User::updateOrCreate(
            ['email' => 'pc@example.com'],
            [
                'name' => 'User PC',
                'password' => Hash::make('pc123456'),
                'role' => 'pc',
                'no_telp' => '081234567891',
                'wilayah_id' => null,
                'ranting_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mwc@example.com'],
            [
                'name' => 'User MWC',
                'password' => Hash::make('mwc123456'),
                'role' => 'mwc',
                'no_telp' => '081234567892',
                'wilayah_id' => $wilayahMwc?->id,
                'ranting_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'ranting@example.com'],
            [
                'name' => 'User Ranting',
                'password' => Hash::make('ranting123456'),
                'role' => 'ranting',
                'no_telp' => '081234567893',
                'wilayah_id' => $wilayahMwc?->id,
                'ranting_id' => $rantingMwc?->id,
            ]
        );
    }
}
