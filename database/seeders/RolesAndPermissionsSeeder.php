<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'acesso painel admin',
            'acesso painel lideranca',
            'acesso painel membro',
            'gerenciar diretoria',
            'gerenciar igrejas',
            'gerenciar usuarios',
            'gerenciar financeiro',
            'gerenciar financeiro_macro',
            'gerenciar tesouraria',
            'gerenciar campanhas',
            'gerenciar pagamentos',
            'visualizar relatorios',
            'gerenciar eventos',
            'gerenciar notificacoes',
            'gerenciar biblia',
            'gerenciar louvor',
            'gerenciar projecao',
            'gerenciar sermoes',
            'gerenciar ministerios',
            'gerenciar homepage',
            'gerenciar intercessao',
            'gerenciar assets',
            'gerenciar mural',
            'gerenciar caravana',
            'visualizar recursos',
        ];

        Permission::query()
            ->whereNotIn('name', $permissions)
            ->get()
            ->each
            ->delete();

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
            'Super Admin',
            'Presidente',
            'Vice-Presidente',
            'Secretário',
            'Tesoureiro',
            'Líder Local',
            'Jovem',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $gabineteAdminPermissions = [
            'acesso painel admin',
            'acesso painel lideranca',
            'acesso painel membro',
            'gerenciar diretoria',
            'gerenciar igrejas',
            'gerenciar usuarios',
            'gerenciar financeiro_macro',
            'gerenciar mural',
            'gerenciar financeiro',
            'gerenciar tesouraria',
            'gerenciar campanhas',
            'gerenciar pagamentos',
            'visualizar relatorios',
            'gerenciar eventos',
            'gerenciar notificacoes',
            'gerenciar biblia',
            'gerenciar louvor',
            'gerenciar projecao',
            'gerenciar sermoes',
            'gerenciar ministerios',
            'gerenciar homepage',
            'gerenciar intercessao',
            'gerenciar assets',
        ];

        Role::findByName('Super Admin', 'web')->syncPermissions(Permission::query()->pluck('name')->all());

        Role::findByName('Presidente', 'web')->syncPermissions($gabineteAdminPermissions);

        Role::findByName('Vice-Presidente', 'web')->syncPermissions($gabineteAdminPermissions);

        Role::findByName('Secretário', 'web')->syncPermissions($gabineteAdminPermissions);

        Role::findByName('Tesoureiro', 'web')->syncPermissions([
            'acesso painel admin',
            'acesso painel lideranca',
            'acesso painel membro',
            'gerenciar diretoria',
            'gerenciar igrejas',
            'gerenciar financeiro_macro',
            'gerenciar mural',
            'gerenciar financeiro',
            'gerenciar tesouraria',
            'gerenciar campanhas',
            'gerenciar pagamentos',
            'visualizar relatorios',
            'gerenciar eventos',
            'gerenciar notificacoes',
        ]);

        Role::findByName('Líder Local', 'web')->syncPermissions([
            'acesso painel lideranca',
            'acesso painel membro',
            'gerenciar caravana',
            'visualizar recursos',
        ]);

        Role::findByName('Jovem', 'web')->syncPermissions([
            'acesso painel membro',
            'visualizar recursos',
        ]);

        $superAdminUser = User::withoutEvents(function () {
            return User::updateOrCreate(
                ['email' => 'admin@jubaf.com.br'],
                [
                    'name' => 'Admin',
                    'sobrenome' => 'JUBAF',
                    'password' => bcrypt('password'),
                    'is_active' => true,
                ]
            );
        });

        $superAdminUser->syncRoles(['Super Admin']);

        if (app()->environment('local', 'development', 'dev')) {
            $devUsers = [
                [
                    'email' => 'superadmin@jubaf.com.br',
                    'name' => 'Super',
                    'sobrenome' => 'Admin Demo',
                    'role' => 'Super Admin',
                ],
                [
                    'email' => 'lideranca@jubaf.com.br',
                    'name' => 'Liderança',
                    'sobrenome' => 'Demo',
                    'role' => 'Líder Local',
                ],
                [
                    'email' => 'membro@jubaf.com.br',
                    'name' => 'Membro',
                    'sobrenome' => 'Demo',
                    'role' => 'Jovem',
                ],
            ];

            foreach ($devUsers as $devUserData) {
                $devUser = User::withoutEvents(function () use ($devUserData) {
                    return User::updateOrCreate(
                        ['email' => $devUserData['email']],
                        [
                            'name' => $devUserData['name'],
                            'sobrenome' => $devUserData['sobrenome'],
                            'password' => bcrypt('password'),
                            'is_active' => true,
                        ]
                    );
                });

                $devUser->syncRoles([$devUserData['role']]);
            }
        }
    }
}
