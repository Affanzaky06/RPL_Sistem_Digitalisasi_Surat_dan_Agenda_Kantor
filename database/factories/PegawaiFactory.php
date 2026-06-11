<?php

namespace Database\Factories;

use App\Models\Pegawai;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Pegawai>
 */
class PegawaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        $tanggalLahir = fake()->dateTimeBetween('1970-01-01', '2000-12-31');
        return [
            'nip' => $this->generateNip($tanggalLahir),
            'nama' => fake()->name(),
            'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
            'password' => Hash::make('Subkoor123'),
            // 'id_bidang' => 'BD07',
            'id_jabatan' => 'J003',
            // 'nip_atasan' => '197004222000081001'
        ];
    }

    // generate nip random
    private function generateNip(DateTime $tanggalLahir): string
    {
        $tmt = $tmt = fake()->dateTimeBetween(
            $tanggalLahir->modify('+22 years'),
            '2024-12-31'
        );

        return
            $tanggalLahir->format('Ymd') .
            $tmt->format('Ym') .
            rand(1, 2) .
            fake()->unique()->numerify('###');
    }
}
