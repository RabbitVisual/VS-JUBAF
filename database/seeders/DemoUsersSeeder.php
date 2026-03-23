<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles (ensure pastor exists for Gabinete Pastoral)
        $adminRole = Role::where('slug', 'admin')->first();
        $memberRole = Role::where('slug', 'membro')->first();
        $pastorRole = Role::firstOrCreate(
            ['slug' => 'pastor'],
            ['name' => 'Pastor', 'description' => 'Acesso ao Gabinete Pastoral e painel admin (gestão ministerial)', 'created_at' => now(), 'updated_at' => now()]
        );

        if (! $adminRole || ! $memberRole) {
            $this->command->warn('Roles não encontradas. Execute primeiro as migrations de roles.');

            return;
        }

        // Create Admin Demo User
        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin Demo',
                'first_name' => 'Admin',
                'last_name' => 'Demo',
                'email' => 'admin@demo.com',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin Demo criado: admin@demo.com / admin123');

        // Create Member Demo User (CPF único para evitar conflito com bases copiadas)
        $member = User::updateOrCreate(
            ['email' => 'membro@demo.com'],
            [
                'name' => 'Membro Demo',
                'first_name' => 'Membro',
                'last_name' => 'Demo',
                'email' => 'membro@demo.com',
                'password' => Hash::make('membro123'),
                'role_id' => $memberRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
                'cpf' => '999.999.999-99',
                'date_of_birth' => '1990-01-15',
                'gender' => 'M',
                'marital_status' => 'solteiro',
                'phone' => '(75) 1234-5678',
                'cellphone' => '(75) 9 9876-5432',
                'address' => 'Rua Exemplo',
                'address_number' => '123',
                'neighborhood' => 'Centro',
                'city' => 'Coração de Maria',
                'state' => 'BA',
                'zip_code' => '44250-000',
                'membership_date' => now()->subMonths(12),
                'is_baptized' => true,
                'baptism_date' => now()->subMonths(6),
                'profession' => 'Desenvolvedor',
                'education_level' => 'superior',
            ]
        );

        $this->command->info('✅ Membro Demo criado: membro@demo.com / membro123');

        // Pastor Demo User (acesso ao Gabinete Pastoral)
        User::updateOrCreate(
            ['email' => 'pastor@demo.com'],
            [
                'name' => 'Pastor Demo',
                'first_name' => 'Pastor',
                'last_name' => 'Demo',
                'email' => 'pastor@demo.com',
                'password' => Hash::make('pastor123'),
                'role_id' => $pastorRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Pastor Demo criado: pastor@demo.com / pastor123');
    }
}
