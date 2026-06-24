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
            'Undangan',
            'Undangan',
            'Pemberitahuan',
            'Edaran'
        ]);

        $tanggalSurat = $this->faker->dateTimeBetween('-2 months', 'now');

        $perihal = match ($jenisSurat) {
            'Undangan' => $this->faker->randomElement([
                'Undangan Rapat Koordinasi',
                'Undangan Sosialisasi Program Kerja',
                'Undangan Kegiatan Monitoring dan Evaluasi',
                'Undangan Rapat Koordinasi Triwulan',
                'Undangan Forum Konsultasi Publik',
                'Undangan Sosialisasi Reformasi Birokrasi',
                'Undangan Monitoring dan Evaluasi Kinerja',
                'Undangan Pembahasan Anggaran',
                'Undangan Penyusunan Renstra',
                'Undangan Audit Internal',
                'Undangan Pembinaan Pegawai',
                'Undangan Evaluasi Program Kerja',
                'Undangan Rakor Lintas Bidang',
                'Undangan Workshop Pelayanan Publik',
                'Undangan Bimbingan Teknis',
                'Undangan Desk Verifikasi Data',
                'Undangan Pembahasan Laporan Keuangan',
                'Undangan Kunjungan Kerja'
            ]),
            'Pemberitahuan' => $this->faker->randomElement([
                'Pemberitahuan Pelaksanaan Kegiatan',
                'Pemberitahuan Perubahan Jadwal',
                'Pemberitahuan Penyesuaian Layanan',
            ]),
            'Edaran' => $this->faker->randomElement([
                'Edaran Jadwal Pelayanan Publik',
                'Edaran Pelaksanaan Apel Pagi',
                'Edaran Penggunaan Fasilitas Kantor',
            ]),
        };

        return [
            'perihal' => $perihal,

            // DI SINI LETAK PERBAIKANNYA: Tambahkan unique() pada pembuat angka
            'nomor_surat' => sprintf(
                '%03d/%s/%d',
                $this->faker->unique()->numberBetween(1, 9999),
                $this->faker->randomElement(['UND', 'EDR', 'PMH', 'PBR']),
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
                'Kementerian Keuangan',
                'Kementerian PANRB',
                'Kementerian Dalam Negeri',
                'Pemprov Jawa Timur',
                'Pemkot Surabaya',
                'Sekretariat Daerah',
                'Bappeda',
                'Dinas Kominfo',
                'Dinas Pendidikan',
                'Dinas Kesehatan',
                'BKD',
                'KPU Provinsi'
            ]),

            'status' => $this->faker->randomElement([
                'Menunggu Verifikasi',
                'Terverifikasi',
                'Ditolak'
            ]),

            'file_scan' => $this->faker->randomElement([
                'undangan-rakor.pdf',
                'undangan-monitoring.pdf',
                'undangan-sosialisasi.pdf',
                'undangan-bimtek.pdf',
                'undangan-evaluasi.pdf',
                'pemberitahuan-jadwal.pdf',
                'pemberitahuan-layanan.pdf',
                'pemberitahuan-kegiatan.pdf',
                'edaran-apel.pdf',
                'edaran-fasilitas.pdf',
            ]),

            'tanggal_verifikasi' => $this->faker->optional(0.7)
                ->dateTimeBetween('-1 month', 'now')
        ];
    }
}
