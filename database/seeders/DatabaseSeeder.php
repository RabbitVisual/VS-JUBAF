<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Idempotent: create only if not exists (safe for production re-seed)
        $user = User::factory()->make([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            $user->getAttributes()
        );

        // Seed demo users in development
        if (app()->environment('local', 'development', 'dev')) {
            $this->call([
                DemoUsersSeeder::class,
            ]);
        }

        // Seed Payment Gateways
        $this->call([
            \Modules\PaymentGateway\Database\Seeders\PaymentGatewayDatabaseSeeder::class,
        ]);

        // Seed Treasury Module
        $this->call([
            \Modules\Treasury\Database\Seeders\TreasuryDatabaseSeeder::class,
        ]);

        // Seed Events Module
        $this->call([
            \Modules\Events\Database\Seeders\EventsDatabaseSeeder::class,
        ]);

        // Notifications: templates de produção (idempotente)
        $this->call([
            \Modules\Notifications\Database\Seeders\NotificationTemplatesSeeder::class,
        ]);

        // Local-only demo data across all church modules
        if (app()->environment('local', 'development', 'dev')) {
            $this->call([
                \Database\Seeders\LocalDemoSeeder::class,
            ]);
        }
    }
}
