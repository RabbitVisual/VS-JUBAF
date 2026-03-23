<?php

namespace Modules\HomePage\Database\Seeders;

use Illuminate\Database\Seeder;

class HomePageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            TestimonialsTableSeeder::class,
            EventsTableSeeder::class,
            GalleryImagesTableSeeder::class,
        ]);
    }
}
