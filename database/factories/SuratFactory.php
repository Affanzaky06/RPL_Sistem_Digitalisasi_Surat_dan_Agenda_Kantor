<?php

namespace Database\Factories;

use App\Models\Surat;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuratFactory extends Factory
{
    protected $model = Surat::class;

    public function definition(): array
    {
        $jenisSurat = $this->faker->randomElement([
            'Undangan',
            'Pemberitahuan',
            'Edaran'
        ]);

        $tanggalSurat = $this->faker->dateTimeBetween('-2 months', 'now');

        return [

            'perihal' => $this->faker->sentence(4),

            'nomor_surat' => sprintf(
                '%03d/%s/%d',
                fake()->numberBetween(1, 999),
                fake()->randomElement(['UND', 'EDR', 'PMH', 'PBR']),
                now()->year
            ),

            'jenis_surat' => $jenisSurat,

            'prioritas' => $this->faker->randomElement([
                'Rendah',
                'Sedang',
                'Tinggi'
            ]),

            'tanggal_surat' => $tanggalSurat,

            'tanggal_kegiatan' => $jenisSurat === 'Undangan'
                ? $this->faker->dateTimeBetween('+1 day', '+1 month')
                : null,

            'lokasi_kegiatan' => $jenisSurat === 'Undangan'
                ? $this->faker->randomElement([
                    'Ruang Rapat Utama',
                    'Aula Kantor',
                    'Ruang Meeting Lt.2',
                    'Gedung Serbaguna'
                ])
                : null,

            'waktu_mulai_kegiatan' => $jenisSurat === 'Undangan'
                ? '08:00'
                : null,

            'waktu_selesai_kegiatan' => $jenisSurat === 'Undangan'
                ? '10:00'
                : null,

            'asal_surat' => $this->faker->randomElement([
                'BPK RI',
                'BPKP',
                'Inspektorat',
                'Pemprov Jawa Timur',
                'Kementerian Keuangan',
                'Sekretariat Daerah'
            ]),

            'status' => $this->faker->randomElement([
                'Menunggu Verifikasi',
                'Terverifikasi',
                'Ditolak'
            ]),

            'file_scan' => 'dummy.pdf',

            'tanggal_verifikasi' => $this->faker->optional(0.7)
                ->dateTimeBetween('-1 month', 'now')
        ];
    }
}
