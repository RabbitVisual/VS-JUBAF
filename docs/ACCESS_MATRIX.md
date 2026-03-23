# Matriz Oficial de Acesso JUBAF

Este documento define a matriz oficial de acesso por papel (role), alinhada ao `database/seeders/RolesAndPermissionsSeeder.php`.

## Roles Oficiais

- Super Admin
- Presidente
- Vice-Presidente
- Secretário
- Tesoureiro
- Líder Local
- Jovem

## Permissões Oficiais

- acesso painel admin
- acesso painel lideranca
- acesso painel membro
- gerenciar igrejas
- gerenciar usuarios
- gerenciar financeiro
- gerenciar tesouraria
- gerenciar campanhas
- gerenciar pagamentos
- visualizar relatorios
- gerenciar eventos
- gerenciar notificacoes
- gerenciar biblia
- gerenciar louvor
- gerenciar projecao
- gerenciar sermoes
- gerenciar ministerios
- gerenciar homepage
- gerenciar intercessao
- gerenciar assets
- gerenciar conselho

## Matriz Role x Permissão

| Role | Permissões |
|---|---|
| Super Admin | Todas as permissões oficiais |
| Presidente | Todas as permissões oficiais |
| Vice-Presidente | acesso painel admin, acesso painel lideranca, gerenciar usuarios, gerenciar igrejas, gerenciar conselho, gerenciar eventos, gerenciar ministerios, gerenciar notificacoes, visualizar relatorios |
| Secretário | acesso painel lideranca, gerenciar usuarios, gerenciar conselho, gerenciar eventos, gerenciar sermoes, gerenciar notificacoes, visualizar relatorios |
| Tesoureiro | acesso painel lideranca, gerenciar financeiro, gerenciar tesouraria, gerenciar campanhas, gerenciar pagamentos, visualizar relatorios |
| Líder Local | acesso painel lideranca, acesso painel membro, gerenciar eventos, gerenciar ministerios, gerenciar louvor, gerenciar projecao, gerenciar sermoes, gerenciar intercessao, gerenciar biblia |
| Jovem | acesso painel membro, gerenciar biblia, gerenciar eventos, gerenciar sermoes, gerenciar intercessao |

## Regras de Governança

- `Super Admin` e `Presidente` sempre recebem 100% das permissões oficiais.
- Toda role de liderança deve possuir `acesso painel lideranca`.
- Permissões legadas de módulos removidos (ex.: EBD legado, gamificação antiga, bot) não devem ser recriadas.
- Novas permissões devem ser adicionadas primeiro no seeder oficial e depois documentadas neste arquivo.

## Fluxo de Atualização

1. Ajustar `database/seeders/RolesAndPermissionsSeeder.php`.
2. Revisar impacto de autorização em controllers, policies e middleware.
3. Atualizar este documento com novas permissões ou mudanças de escopo.
4. Reexecutar seeders no ambiente apropriado.

