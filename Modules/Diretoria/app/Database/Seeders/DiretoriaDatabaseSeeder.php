<?php

namespace Modules\Diretoria\App\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Diretoria\App\Models\DiretoriaMember;

class DiretoriaDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        // Criar um usuário admin para teste (se não existir)
        $adminUser = User::firstOrCreate(
            ['email' => 'conselho@igrejabatista.com'],
            [
                'name' => 'Administrador da Diretoria',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Criar membro da diretoria (presidente)
        DiretoriaMember::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'diretoria_position' => 'Presidente',
                'diretoria_role' => 'president',
                'term_start' => now()->subYear(),
                'term_end' => null,
                'is_active' => true,
                'responsibilities' => 'Administração geral da diretoria, coordenação de reuniões e supervisão de decisões.',
                'permissions' => ['*'],
            ]
        );

        // Criar outros membros de exemplo (lideranca, diáconos, secretário, tesoureiro, membros)
        $members = [
            [
                'name' => 'João Silva',
                'email' => 'joao.silva@igrejabatista.com',
                'position' => 'Vice-Presidente',
                'role' => 'vice_president',
                'responsibilities' => 'Substituir o presidente quando necessário, auxiliar na coordenação.',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@igrejabatista.com',
                'position' => 'Secretária',
                'role' => 'secretary',
                'responsibilities' => 'Redigir atas das reuniões, manter registros oficiais.',
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro.oliveira@igrejabatista.com',
                'position' => 'Tesoureiro',
                'role' => 'treasurer',
                'responsibilities' => 'Gerenciar finanças da igreja, apresentar relatórios financeiros.',
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@igrejabatista.com',
                'position' => 'Diácona',
                'role' => 'deacon',
                'responsibilities' => 'Auxiliar no serviço prático e assistência aos membros.',
            ],
            [
                'name' => 'Carlos Souza',
                'email' => 'carlos.souza@igrejabatista.com',
                'position' => 'Diácono',
                'role' => 'deacon',
                'responsibilities' => 'Serviço e assistência à congregação.',
            ],
            [
                'name' => 'Fernanda Lima',
                'email' => 'fernanda.lima@igrejabatista.com',
                'position' => 'Membro',
                'role' => 'member',
                'responsibilities' => 'Participar das reuniões e contribuir com decisões.',
            ],
        ];

        foreach ($members as $memberData) {
            $user = User::firstOrCreate(
                ['email' => $memberData['email']],
                [
                    'name' => $memberData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            DiretoriaMember::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'diretoria_position' => $memberData['position'],
                    'diretoria_role' => $memberData['role'],
                    'term_start' => now()->subMonths(rand(1, 12)),
                    'term_end' => null,
                    'is_active' => true,
                    'responsibilities' => $memberData['responsibilities'],
                    'permissions' => in_array($memberData['role'], ['president', 'vice_president'])
                        ? ['meetings.create', 'approvals.review', 'members.manage'] : ['meetings.view', 'approvals.view'],
                ]
            );
        }
    }
}
