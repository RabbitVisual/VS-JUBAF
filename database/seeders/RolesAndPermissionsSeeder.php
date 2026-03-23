<?php

namespace Database\Seeders;

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
            'gerenciar igrejas',
            'gerenciar usuarios',
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
            'gerenciar conselho',
        ];

        // Remove permissões legadas que não fazem mais parte do escopo atual.
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

        $allPermissions = Permission::query()->pluck('name')->all();

        Role::findByName('Super Admin', 'web')->syncPermissions($allPermissions);
        Role::findByName('Presidente', 'web')->syncPermissions($allPermissions);

        Role::findByName('Vice-Presidente', 'web')->syncPermissions([
            'acesso painel lideranca',
            'acesso painel admin',
            'gerenciar usuarios',
            'gerenciar igrejas',
            'gerenciar conselho',
            'gerenciar eventos',
            'gerenciar ministerios',
            'gerenciar notificacoes',
            'visualizar relatorios',
        ]);

        Role::findByName('Secretário', 'web')->syncPermissions([
            'acesso painel lideranca',
            'gerenciar usuarios',
            'gerenciar conselho',
            'gerenciar eventos',
            'gerenciar sermoes',
            'gerenciar notificacoes',
            'visualizar relatorios',
        ]);

        Role::findByName('Tesoureiro', 'web')->syncPermissions([
            'acesso painel lideranca',
            'gerenciar financeiro',
            'gerenciar tesouraria',
            'gerenciar campanhas',
            'gerenciar pagamentos',
            'visualizar relatorios',
        ]);

        Role::findByName('Líder Local', 'web')->syncPermissions([
            'acesso painel lideranca',
            'acesso painel membro',
            'gerenciar eventos',
            'gerenciar ministerios',
            'gerenciar louvor',
            'gerenciar projecao',
            'gerenciar sermoes',
            'gerenciar intercessao',
            'gerenciar biblia',
        ]);

        Role::findByName('Jovem', 'web')->syncPermissions([
            'acesso painel membro',
            'gerenciar biblia',
            'gerenciar eventos',
            'gerenciar sermoes',
            'gerenciar intercessao',
        ]);

        $leaderRoles = [
            'Super Admin',
            'Presidente',
            'Vice-Presidente',
            'Secretário',
            'Tesoureiro',
            'Líder Local',
        ];

        foreach ($leaderRoles as $roleName) {
            Role::findByName($roleName, 'web')->givePermissionTo('acesso painel lideranca');
        }
    }
}
