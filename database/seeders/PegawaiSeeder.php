<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::factory()->create([
            'nip' => '197004222000081001',
            'nama' => 'Affan Dzaky',
            'tanggal_lahir' => '1970-04-22',
            'password' => Hash::make('KepalaKantor123'),
            'id_bidang' => null,
            'id_jabatan' => 'J001',
            'nip_atasan' => null
        ]);

        $kabidKeuangan = Pegawai::factory()->create([
            'nip' => '198305122012102001',
            'nama' => 'Estri Maharani',
            'tanggal_lahir' => '1983-05-12',
            'password' => Hash::make('Kabid123'),
            'id_bidang' => 'B001',
            'id_jabatan' => 'J002',
            'nip_atasan' => '197004222000081001'
        ]);

        $kabidUmum = Pegawai::factory()->create([
            'nip' => '198501252015011004',
            'nama' => 'Ryan Haqqi',
            'tanggal_lahir' => '1985-01-25',
            'password' => Hash::make('Kabid123'),
            'id_bidang' => 'B002',
            'id_jabatan' => 'J002',
            'nip_atasan' => '197004222000081001'
        ]);

        $kabidPerencanaan = Pegawai::factory()->create([
            'nip' => '198402052014021003',
            'nama' => 'Dzaky Muammar',
            'tanggal_lahir' => '1984-02-05',
            'password' => Hash::make('Kabid123'),
            'id_bidang' => 'B003',
            'id_jabatan' => 'J002',
            'nip_atasan' => '197004222000081001'
        ]);

        $kabidPP = Pegawai::factory()->create([
            'nip' => '198012252010051006',
            'nama' => 'Wahyu Fahri',
            'tanggal_lahir' => '1980-12-25',
            'password' => Hash::make('Kabid123'),
            'id_bidang' => 'B004',
            'id_jabatan' => 'J004',
            'nip_atasan' => '197004222000081001'
        ]);

        // Seeder frontliner, sekretaris dan kepegawaian

        Pegawai::factory()->create([
            'nip' => '198102052011051022',
            'nama' => 'Harjo',
            'tanggal_lahir' => '1981-02-05',
            'password' => Hash::make('Frontliner123'),
            'id_bidang' => 'B004',
            'id_jabatan' => 'J007',
            'nip_atasan' => '197004222000081001'
        ]);

        Pegawai::factory()->create([
            'nip' => '198106222011052012',
            'nama' => 'Hartini',
            'tanggal_lahir' => '1981-06-22',
            'password' => Hash::make('Sekretaris123'),
            'id_bidang' => null,
            'id_jabatan' => 'J006',
            'nip_atasan' => '197004222000081001'
        ]);

        Pegawai::factory()->create([
            'nip' => '198212122012021005',
            'nama' => 'Hartono',
            'tanggal_lahir' => '1982-12-12',
            'password' => Hash::make('Kepegawaian123'),
            'id_bidang' => null,
            'id_jabatan' => 'J005',
            'nip_atasan' => '197004222000081001'
        ]);

        // Factory data pegawai random tiap bidang
        $subkoor = Pegawai::factory(5)->create([
            'id_bidang' => $kabidKeuangan->id_bidang,
            'nip_atasan' => $kabidKeuangan->nip,
        ]);

        $subkoor2 = Pegawai::factory(5)->create([
            'id_bidang' => $kabidUmum->id_bidang,
            'nip_atasan' => $kabidUmum->nip,
        ]);

        $subkoor3 = Pegawai::factory(5)->create([
            'id_bidang' => $kabidPerencanaan->id_bidang,
            'nip_atasan' => $kabidPerencanaan->nip,
        ]);

        $subkoor4 = Pegawai::factory(5)->create([
            'id_bidang' => $kabidPP->id_bidang,
            'nip_atasan' => $kabidPP->nip,
        ]);


        $semuaRombonganSubkoor = [
            $subkoor,
            $subkoor2,
            $subkoor3,
            $subkoor4
        ];

        // LOOPING PEMBUATAN STAFF
        // Untuk setiap rombongan bidang...
        foreach ($semuaRombonganSubkoor as $rombongan) {

            // Untuk setiap individu subkoor di dalam bidang tersebut...
            $rombongan->each(function ($individuSubkoor) {

                // Buatkan 5 orang staff yang menginduk ke NIP individu subkoor ini
                Pegawai::factory()->count(5)->create([
                    'id_jabatan' => 'J004',
                    'id_bidang' => $individuSubkoor->id_bidang, // Staff mengikuti bidang atasannya
                    'password' => Hash::make('Staf123'),
                    'nip_atasan' => $individuSubkoor->nip, // <-- SEKARANG INI VALID!
                ]);
            });
        }
    }
}
