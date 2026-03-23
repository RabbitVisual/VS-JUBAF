<?php

namespace Modules\Worship\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Worship\App\Models\WorshipInstrument;

class WorshipInstrumentsSeeder extends Seeder
{
    public function run()
    {
        $instruments = [
            ['name' => 'Vocais', 'slug' => 'vocals', 'icon' => 'microphone'],
            ['name' => 'Violão', 'slug' => 'acoustic-guitar', 'icon' => 'music-note'],
            ['name' => 'Guitarra', 'slug' => 'electric-guitar', 'icon' => 'lightning-bolt'],
            ['name' => 'Baixo', 'slug' => 'bass', 'icon' => 'music-note'],
            ['name' => 'Bateria', 'slug' => 'drums', 'icon' => 'sparkles'],
            ['name' => 'Teclado', 'slug' => 'keys', 'icon' => 'desktop-computer'],
        ];

        foreach ($instruments as $instrument) {
            WorshipInstrument::firstOrCreate(['slug' => $instrument['slug']], $instrument);
        }
    }
}
