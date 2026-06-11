<?php

namespace Database\Seeders;

use Database\Seeders\SuratSeeder;
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
        $this->call([BidangSeeder::class, JabatanSeeder::class, PegawaiSeeder::class, SuratSeeder::class]);
    }
}
