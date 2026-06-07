<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jabatan::create([
            'id_jabatan' => 'J001',
            'nama_jabatan' => 'Kepala Kantor',
        ]);

        Jabatan::create([
            'id_jabatan' => 'J002',
            'nama_jabatan' => 'Kepala Bidang',
        ]);

        Jabatan::create([
            'id_jabatan' => 'J003',
            'nama_jabatan' => 'Subkoor',
        ]);

        Jabatan::create([
            'id_jabatan' => 'J004',
            'nama_jabatan' => 'Staf',
        ]);

        Jabatan::create([
            'id_jabatan' => 'J005',
            'nama_jabatan' => 'Kepegawaian',
        ]);

        Jabatan::create([
            'id_jabatan' => 'J006',
            'nama_jabatan' => 'Sekretaris',
        ]);

        Jabatan::create([
            'id_jabatan' => 'J007',
            'nama_jabatan' => 'Front Liner',
        ]);
    }
}
