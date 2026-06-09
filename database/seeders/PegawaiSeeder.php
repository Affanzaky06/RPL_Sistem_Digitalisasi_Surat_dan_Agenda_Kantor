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

        $kabidHukum = Pegawai::factory()->create([
            'nip' => '198105152011022006',
            'nama' => 'Atallah Mutmainah',
            'tanggal_lahir' => '1981-05-15',
            'password' => Hash::make('Kabid123'),
            'id_bidang' => 'B005',
            'id_jabatan' => 'J002',
            'nip_atasan' => '197004222000081001'
        ]);

        $kabidArsip = Pegawai::factory()->create([
            'nip' => '197908222009011012',
            'nama' => 'Sutarjo',
            'tanggal_lahir' => '1979-08-22',
            'password' => Hash::make('Kabid123'),
            'id_bidang' => 'B006',
            'id_jabatan' => 'J002',
            'nip_atasan' => '197004222000081001'
        ]);

        $kabidPI = Pegawai::factory()->create([
            'nip' => '197909122009022002',
            'nama' => 'Yulia Sari',
            'tanggal_lahir' => '1979-09-12',
            'password' => Hash::make('Kabid123'),
            'id_bidang' => 'B007',
            'id_jabatan' => 'J002',
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
        Pegawai::factory(5)->create([
            'id_bidang' => $kabidKeuangan->id_bidang,
            'nip_atasan' => $kabidKeuangan->nip,
        ]);

        Pegawai::factory(5)->create([
            'id_bidang' => $kabidUmum->id_bidang,
            'nip_atasan' => $kabidUmum->nip,
        ]);

        Pegawai::factory(5)->create([
            'id_bidang' => $kabidPerencanaan->id_bidang,
            'nip_atasan' => $kabidPerencanaan->nip,
        ]);

        Pegawai::factory(5)->create([
            'id_bidang' => $kabidPP->id_bidang,
            'nip_atasan' => $kabidPP->nip,
        ]);

        Pegawai::factory(5)->create([
            'id_bidang' => $kabidHukum->id_bidang,
            'nip_atasan' => $kabidHukum->nip,
        ]);

        Pegawai::factory(5)->create([
            'id_bidang' => $kabidArsip->id_bidang,
            'nip_atasan' => $kabidArsip->nip,
        ]);

        Pegawai::factory(5)->create([
            'id_bidang' => $kabidPI->id_bidang,
            'nip_atasan' => $kabidPI->nip,
        ]);
    }
}
