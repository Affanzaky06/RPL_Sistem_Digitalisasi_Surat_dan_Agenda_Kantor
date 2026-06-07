<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bidang::create([
            'id_bidang' => 'B001',
            'nama_bidang' => 'Keuangan',
        ]);

        Bidang::create([
            'id_bidang' => 'B002',
            'nama_bidang' => 'Umum',
        ]);

        Bidang::create([
            'id_bidang' => 'B003',
            'nama_bidang' => 'Perencanaan',
        ]);

        Bidang::create([
            'id_bidang' => 'B004',
            'nama_bidang' => 'Pelayanan Publik',
        ]);

        Bidang::create([
            'id_bidang' => 'B005',
            'nama_bidang' => 'Hukum',
        ]);

        Bidang::create([
            'id_bidang' => 'B006',
            'nama_bidang' => 'Kearsipan',
        ]);

        Bidang::create([
            'id_bidang' => 'B007',
            'nama_bidang' => 'Pengawasan Internal',
        ]);
    }
}
