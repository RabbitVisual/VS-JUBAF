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
            \Modules\Worship\Database\Seeders\WorshipDatabaseSeeder::class,
            \Modules\EBD\Database\Seeders\EBDDatabaseSeeder::class,
            \Modules\HomePage\Database\Seeders\HomePageDatabaseSeeder::class,
            \Modules\Gamification\Database\Seeders\GamificationDatabaseSeeder::class,
            \Modules\Treasury\Database\Seeders\TreasuryDatabaseSeeder::class,
            \Modules\Events\Database\Seeders\EventsDatabaseSeeder::class,
            \Modules\ChurchCouncil\App\Database\Seeders\ChurchCouncilDatabaseSeeder::class,
            \Modules\SocialAction\Database\Seeders\SocialActionDatabaseSeeder::class,
            \Modules\Intercessor\Database\Seeders\IntercessorDatabaseSeeder::class,
            \Modules\Sermons\Database\Seeders\SermonsDatabaseSeeder::class,
            \Modules\Ministries\Database\Seeders\MinistriesDatabaseSeeder::class,
            \Modules\Assets\Database\Seeders\AssetsDatabaseSeeder::class,
            \Modules\Projection\Database\Seeders\ProjectionDatabaseSeeder::class,
            \Modules\MemberPanel\Database\Seeders\MemberPanelDatabaseSeeder::class,
            \Modules\Admin\Database\Seeders\AdminDatabaseSeeder::class,
            \Modules\Notifications\Database\Seeders\NotificationsDatabaseSeeder::class,
            \Modules\Bible\Database\Seeders\BibleDatabaseSeeder::class,
            \Modules\Marketplace\Database\Seeders\MarketplaceDatabaseSeeder::class,
        ]);
    }
}

