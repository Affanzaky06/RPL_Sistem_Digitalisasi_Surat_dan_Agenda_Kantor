<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 2. Buat data pegawai dengan password yang DI-HASH
        Pegawai::create([
            'nip' => '003',
            'nama' => 'Kabid',
            'password' => Hash::make('Kabid123'),
            'id_bidang' => 'BD03',
            'id_jabatan' => 'J003',
            'nip_atasan' => '001'
        ]);
    }
}
