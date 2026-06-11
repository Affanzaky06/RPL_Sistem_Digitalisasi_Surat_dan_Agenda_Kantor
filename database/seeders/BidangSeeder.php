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
            'nama_bidang' => 'Infrastruktur Kewilayahan Perekonomian SDA',
        ]);

        Bidang::create([
            'id_bidang' => 'B002',
            'nama_bidang' => 'Pemerintahan dan Pembangunan Manusia',
        ]);

        Bidang::create([
            'id_bidang' => 'B003',
            'nama_bidang' => 'Perencanaan Pengendalian dan Evaluasi',
        ]);

        Bidang::create([
            'id_bidang' => 'B004',
            'nama_bidang' => 'Riset dan Inovasi',
        ]);
    }
}
