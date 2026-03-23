<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for local demo data across modules.
     */
    public function run(): void
    {
        $this->call([
            \Modules\HomePage\Database\Seeders\HomePageDatabaseSeeder::class,
            \Modules\Treasury\Database\Seeders\TreasuryDatabaseSeeder::class,
            \Modules\Events\Database\Seeders\EventsDatabaseSeeder::class,
            \Modules\ChurchCouncil\App\Database\Seeders\ChurchCouncilDatabaseSeeder::class,
            \Modules\Sermons\Database\Seeders\SermonsDatabaseSeeder::class,
            \Modules\MemberPanel\Database\Seeders\MemberPanelDatabaseSeeder::class,
            \Modules\Admin\Database\Seeders\AdminDatabaseSeeder::class,
            \Modules\Notifications\Database\Seeders\NotificationsDatabaseSeeder::class,
            \Modules\Bible\Database\Seeders\BibleDatabaseSeeder::class,
            \Modules\Igrejas\Database\Seeders\IgrejasDatabaseSeeder::class,
            \Modules\Comunicacao\Database\Seeders\ComunicacaoDatabaseSeeder::class,
        ]);
    }
}

