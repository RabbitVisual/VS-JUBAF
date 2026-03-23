# Plano 1 / Fase 1 Fundação Vertex JUBAF

## Limpeza Modular JUBAF

Concluir a limpeza estrutural pós-exclusão de módulos, remover bots/menu órfãos e preparar a nova base com os módulos Igrejas e Comunicacao sem quebrar o boot do Laravel.

todos:

- id: clean-autoload-and-modules
  content: Remover namespaces PSR-4 dos módulos excluídos e validar registro de módulos ativos.
  status: completed
- id: remove-bots-and-orphan-routes
  content: Eliminar referências CbavBot/EliasBot e blocos de rotas para módulos removidos.
  status: completed
- id: clean-sidebars-layouts
  content: Atualizar menus/layouts Admin, MemberPanel e liderancapanel removendo links órfãos.
  status: completed
- id: create-new-jubaf-modules
  content: Gerar módulos Igrejas e Comunicacao com models/migrations solicitados.
  status: completed
- id: run-stability-validation
  content: Executar autoload/clear caches/discover/routes e validar boot do sistema.
  status: completed

# Limpeza e Fundação Vertex JUBAF

## Objetivo

Eliminar referências quebradas dos módulos removidos e dos bots, estabilizar autoload/rotas/layouts, e criar a fundação dos novos módulos `Igrejas` e `Comunicacao` com modelos/migrations iniciais.

## Escopo confirmado no estado atual

- As pastas dos módulos removidos já não existem em `Modules/`.
- `modules_statuses.json` já está sem esses módulos.
- Ainda há referências órfãs em:
    - [C:/laragon/www/JUBAF/composer.json](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/composer.json)
    - [C:/laragon/www/JUBAF/routes/admin.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/routes/admin.php)
    - [C:/laragon/www/JUBAF/routes/member.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/routes/member.php)
    - [C:/laragon/www/JUBAF/routes/liderancaal.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/routes/liderancaal.php)
    - [C:/laragon/www/JUBAF/Modules/Admin/resources/views/components/sidebar.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Admin/resources/views/components/sidebar.blade.php)
    - [C:/laragon/www/JUBAF/Modules/MemberPanel/resources/views/components/sidebar.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/MemberPanel/resources/views/components/sidebar.blade.php)
    - [C:/laragon/www/JUBAF/Modules/liderancapanel/resources/views/components/sidebar.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/liderancapanel/resources/views/components/sidebar.blade.php)

## Plano de implementação

1. **Higienizar autoload e registro de módulos**

- Remover do `composer.json` todos os namespaces PSR-4 dos módulos excluídos (`Gamification`, `Assets`, `EBD`, `Marketplace`, `SocialAction`, `Projection`, `Intercessor`, `Ministries`, `Worship`).
- Verificar se restaram referências textuais desses módulos em bootstrap/providers/config de módulos.

1. **Remover Bots (CbavBot/EliasBot) do core e UI**

- Excluir rotas de bot em `routes/admin.php` e `routes/member.php`.
- Remover controller/view de configuração do bot no Admin (incluindo entradas de menu).
- Remover widgets/partials/blades do Elias em telas administrativas/liderancaais (especialmente no módulo Sermons).
- Eliminar chamadas de serviços de bot em controllers que hoje invocam análise Elias.

1. **Limpar rotas órfãs dos módulos removidos**

- Em `routes/admin.php`, remover blocos de rotas de `Ministries`, `EBD`, `Marketplace`, `Intercessor`, `Projection`, `Worship`, `SocialAction`, `Assets`, e rotas de gamificação ligadas aos módulos removidos.
- Em `routes/member.php`, remover blocos de `Ministries`, `cbav-bot`, `Marketplace`, `EBD`, `Intercessor`, `Worship`, `Projection`.
- Em `routes/liderancaal.php`, remover seções dependentes de `EBD`, `Ministries` e rotas de oração acopladas ao módulo removido.

1. **Limpar menus/layouts nos painéis**

- Remover links e guards (`Module::isEnabled(...)`) para os módulos removidos em sidebars/navbars de Admin, MemberPanel e liderancapanel.
- Garantir que itens de menu restantes apontem apenas para módulos existentes.

1. **Gerar novos módulos JUBAF**

- Criar módulo `Igrejas` via laravel-modules.
- Criar migration + model `Igreja` com campos: `nome`, `lideranca_titular`, `lider_jovens`, `cidade`, `estado`, `logo_path`.
- Criar módulo `Comunicacao` via laravel-modules.
- Criar migration + model `Postagem` com campos: `titulo`, `conteudo` (text), `tipo` enum (`edital`, `ata`, `aviso`, `noticia`), `anexo_path`, `user_id`.
- Garantir registro/autoload dos novos módulos no `composer.json` e em `modules_statuses.json`.

1. **Estabilização e validação operacional**

- Rodar `composer dump-autoload`.
- Rodar `php artisan optimize:clear` (inclui cache de rota/view/config/compiled).
- Rodar `php artisan package:discover --ansi` e `php artisan route:list` para confirmar boot sem referências quebradas.
- Rodar migrações novas e validar que os modelos/migrations dos módulos novos carregam corretamente.

## Entregáveis finais

- Código limpo sem referências aos módulos removidos e sem Bot Elias/CbavBot.
- Menus dos três painéis sem links órfãos.
- Módulos `Igrejas` e `Comunicacao` criados com models/migrations solicitados.
- Resumo final com: lista de pastas removidas (já inexistentes), arquivos limpos e status de execução/saúde do sistema.

# Plano 2 / Fase 2

## Despoluir Migrations Seeders
- overview: Despoluir migrations/seeders da raiz, unificar schema de usuários no arquivo base do Laravel e alinhar o seeding oficial da JUBAF com roles/permissões Spatie e foco em LiderancaPanel.

- todos:
  - id: map-delete-migrations
    content: Remover migrations lixo e modularizadas indevidamente na raiz
    status: in_progress
  - id: rewrite-users-migration
    content: Substituir migration base de users com schema unificado JUBAF
    status: pending
  - id: update-user-model
    content: Atualizar User model com fillable novo e traits HasRoles/SoftDeletes
    status: pending
  - id: rework-seeders
    content: Remover seeders demo, criar RolesAndPermissionsSeeder e simplificar DatabaseSeeder
    status: pending
  - id: final-static-validation
    content: Validar lints e listar com precisão arquivos removidos/modificados
    status: pending

# Plano de Despoluição e Padronização (JUBAF)

## Escopo confirmado

- Atuar **somente em arquivos** (sem executar `php artisan migrate`).
- Limpar migrations legadas na raiz e manter migrations funcionais por módulo em `Modules/*/database/migrations`.
- Consolidar `users` na migration base e alinhar `User` model + seeders oficiais.

## 1) Despoluição de `database/migrations`

- Remover arquivos explicitamente listados pelo pedido que existem hoje na raiz:
  - [database/migrations/2025_12_22_000001_create_user_badges_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2025_12_22_000001_create_user_badges_table.php)
  - [database/migrations/2025_12_29_205554_create_testimonials_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2025_12_29_205554_create_testimonials_table.php)
  - [database/migrations/2025_12_29_205612_create_gallery_images_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2025_12_29_205612_create_gallery_images_table.php)
  - [database/migrations/2026_01_23_174743_create_contact_messages_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_01_23_174743_create_contact_messages_table.php)
  - [database/migrations/2026_01_22_023422_drop_ebd_tables.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_01_22_023422_drop_ebd_tables.php)
  - [database/migrations/2026_02_03_045041_add_xp_and_level_to_users_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_02_03_045041_add_xp_and_level_to_users_table.php)
  - [database/migrations/2026_02_03_055014_add_can_project_to_users_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_02_03_055014_add_can_project_to_users_table.php)
  - [database/migrations/2026_02_19_160000_add_cbav_bot_enabled_to_users_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_02_19_160000_add_cbav_bot_enabled_to_users_table.php)
  - [database/migrations/2026_01_20_201007_create_user_photos_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_01_20_201007_create_user_photos_table.php)
  - [database/migrations/2026_01_21_055158_add_birth_date_to_users_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_01_21_055158_add_birth_date_to_users_table.php)
  - [database/migrations/2026_03_07_120000_add_two_factor_to_users_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_03_07_120000_add_two_factor_to_users_table.php)
  - [database/migrations/2026_03_08_000001_add_lideranca_role.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_03_08_000001_add_lideranca_role.php)
- Remover também migrations de domínio modular que estão indevidamente na raiz:
  - Events: [database/migrations/2025_12_29_205603_create_events_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2025_12_29_205603_create_events_table.php)
  - Newsletter: [database/migrations/2025_12_29_210216_create_newsletter_subscribers_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2025_12_29_210216_create_newsletter_subscribers_table.php)
  - Financial goals: [database/migrations/2026_02_07_000000_add_icon_and_color_to_financial_goals_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_02_07_000000_add_icon_and_color_to_financial_goals_table.php) e [database/migrations/2026_02_07_004935_add_icon_and_color_to_financial_goals_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_02_07_004935_add_icon_and_color_to_financial_goals_table.php)
  - Bible favorites: [database/migrations/2026_01_24_142949_add_color_to_bible_favorites_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_01_24_142949_add_color_to_bible_favorites_table.php) e [database/migrations/2026_01_26_100536_add_color_and_note_to_bible_favorites_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/2026_01_26_100536_add_color_and_note_to_bible_favorites_table.php)

## 2) Unificar `users` no arquivo base

- Substituir integralmente [database/migrations/0001_01_01_000000_create_users_table.php](../../../Users/Administrator/.cursor/plans/database/migrations/0001_01_01_000000_create_users_table.php) para conter apenas o schema alvo JUBAF em `users`:
  - `id`, `name`, `sobrenome` (nullable), `email` (unique), `email_verified_at`, `password`, `whatsapp` (nullable), `data_nascimento` (nullable), `cpf` (unique nullable), `avatar` (nullable), `is_active` (default true), `igreja_id` (unsignedBigInteger nullable), `remember_token`, `timestamps`, `softDeletes`.
- Manter `password_reset_tokens` e `sessions` no mesmo arquivo (padrão Laravel atual).
- Não criar FK de `igreja_id` agora (seguir ordem de boot do módulo Igrejas, conforme pedido).

## 3) Ajustar `App\Models\User`

- Em [app/Models/User.php](../../../Users/Administrator/.cursor/plans/app/Models/User.php):
  - Garantir `use HasRoles;` (Spatie) e `use SoftDeletes;` no model/traits.
  - Atualizar `$fillable` para refletir apenas os campos da nova unificação de `users`.
  - Manter o restante do model funcional sem quebra imediata, mas alinhando nomes (`avatar`, `data_nascimento`, `sobrenome`, `whatsapp`, etc.) para consistência com migration base.

## 4) Seeder oficial JUBAF

- Remover seeders de demonstração da raiz:
  - [database/seeders/DemoUsersSeeder.php](../../../Users/Administrator/.cursor/plans/database/seeders/DemoUsersSeeder.php)
  - [database/seeders/LocalDemoSeeder.php](../../../Users/Administrator/.cursor/plans/database/seeders/LocalDemoSeeder.php)
  - (Se houver `GamificationSeeder` na raiz, também remover; hoje não foi encontrado.)
- Criar/substituir [database/seeders/RolesAndPermissionsSeeder.php](../../../Users/Administrator/.cursor/plans/database/seeders/RolesAndPermissionsSeeder.php) com:
  - Permissões macro mínimas: `acesso painel admin`, `acesso painel lideranca`, `gerenciar igrejas`, `gerenciar usuarios`, `gerenciar financeiro` + essenciais operacionais (ex.: `visualizar relatorios`, `gerenciar eventos`, `gerenciar notificacoes`).
  - Roles exatas: `Super Admin`, `Presidente`, `Vice-Presidente`, `Secretário`, `Tesoureiro`, `Líder Local`, `Jovem`.
  - Regras: todas permissões para `Super Admin` e `Presidente`; permissão `acesso painel lideranca` para todos os papéis de liderança.
- Atualizar [database/seeders/DatabaseSeeder.php](../../../Users/Administrator/.cursor/plans/database/seeders/DatabaseSeeder.php) para chamar apenas:
  - `RolesAndPermissionsSeeder::class`
  - e o seeder de admin principal se aplicável (na base atual, [Modules/Admin/database/seeders/AdminDatabaseSeeder.php](../../../Users/Administrator/.cursor/plans/Modules/Admin/database/seeders/AdminDatabaseSeeder.php), hoje vazio).

## 5) Validação estática final (sem migrate)

- Conferir diffs e lista final de arquivos apagados/modificados.
- Conferir lints dos arquivos alterados.
- Entregar relatório objetivo com:
  - arquivos removidos,
  - arquivos modificados/criados,
  - observações de compatibilidade imediata (se houver referências legadas a `photo`, `date_of_birth`, etc.).


## Plano 3 / Fase 3
