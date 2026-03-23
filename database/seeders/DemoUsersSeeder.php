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
        // Get roles (ensure lideranca exists for Gabinete liderancaal)
        $adminRole = Role::where('slug', 'admin')->first();
        $memberRole = Role::where('slug', 'membro')->first();
        $liderancaRole = Role::firstOrCreate(
            ['slug' => 'lideranca'],
            ['name' => 'lideranca', 'description' => 'Acesso ao Gabinete liderancaal e painel admin (gestão ministerial)', 'created_at' => now(), 'updated_at' => now()]
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

        // lideranca Demo User (acesso ao Gabinete liderancaal)
        User::updateOrCreate(
            ['email' => 'lideranca@demo.com'],
            [
                'name' => 'lideranca Demo',
                'first_name' => 'lideranca',
                'last_name' => 'Demo',
                'email' => 'lideranca@demo.com',
                'password' => Hash::make('lideranca123'),
                'role_id' => $liderancaRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ lideranca Demo criado: lideranca@demo.com / lideranca123');
    }
}
