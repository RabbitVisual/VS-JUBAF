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

- Remover do `composer.json` todos os namespaces PSR-4 dos módulos excluídos (`Gamification`, `Assets`, `EBD`, `Marketplace`, `SocialAction`, `Projection`, `Ministries`, `Worship`).
- Verificar se restaram referências textuais desses módulos em bootstrap/providers/config de módulos.

1. **Remover Bots (CbavBot/EliasBot) do core e UI**

- Excluir rotas de bot em `routes/admin.php` e `routes/member.php`.
- Remover controller/view de configuração do bot no Admin (incluindo entradas de menu).
- Remover widgets/partials/blades do Elias em telas administrativas/liderancaais (especialmente no módulo Sermons).
- Eliminar chamadas de serviços de bot em controllers que hoje invocam análise Elias.

1. **Limpar rotas órfãs dos módulos removidos**

- Em `routes/admin.php`, remover blocos de rotas de `Ministries`, `EBD`, `Marketplace`, `Projection`, `Worship`, `SocialAction`, `Assets`, e rotas de gamificação ligadas aos módulos removidos.
- Em `routes/member.php`, remover blocos de `Ministries`, `cbav-bot`, `Marketplace`, `EBD`, `Worship`, `Projection`.
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

Frase oficial de encerramento:
Menções documentais legadas removidas; base alinhada 100% ao escopo JUBAF atual.
## Reset DB e build

- overview: Executar limpeza de cache, reset completo do banco com seed, garantir usuário Super Admin fixo e validar build frontend sem erros.
- todos:
  - id: verify-seeder-admin
    content: Adicionar/validar criação idempotente do usuário fixo Super Admin no seeder apropriado
    status: pending
  - id: run-cache-cleanup
    content: Executar todos os comandos de limpeza de cache/autoload em sequência
    status: pending
  - id: run-fresh-seed-loop
    content: Executar migrate:fresh --seed e corrigir migration se falhar até ficar verde
    status: pending
  - id: build-frontend
    content: Executar npm run build e confirmar compilação
    status: pending
  - id: report-results
    content: Reportar status final e credenciais de acesso admin
    status: pending

# Plano para reset de banco e validação completa

## Objetivo

Executar a preparação completa do ambiente Laravel (cache + migrate/seed + build), garantindo credencial fixa de Super Admin para acesso após o reset.

## Escopo e arquivos-alvo

- Validar e, se necessário, ajustar seeder principal em [C:\laragon\www\JUBAF\database\seeders\DatabaseSeeder.php](../../../Users/Administrator/.cursor/plans/C:\laragon\www\JUBAF\database\seeders\DatabaseSeeder.php)
- Garantir criação do Super Admin em [C:\laragon\www\JUBAF\database\seeders\RolesAndPermissionsSeeder.php](../../../Users/Administrator/.cursor/plans/C:\laragon\www\JUBAF\database\seeders\RolesAndPermissionsSeeder.php)
- (Somente se erro de migrate) corrigir migration específica que falhar durante `migrate:fresh --seed`

## Estratégia de execução

1. Rodar sequência de limpeza de cache/autoload:

- `composer dump-autoload`
- `php artisan optimize:clear`
- `php artisan config:clear`
- `php artisan cache:clear`
- `php artisan view:clear`

1. Garantir usuário Super Admin fixo no seeder antes do reset final:

- `name = Admin`
- `sobrenome = JUBAF`
- `email = admin@jubaf.com.br`
- `password = bcrypt('password')` (ou `Hash::make('password')` equivalente)
- atribuição `assignRole('Super Admin')`
- comportamento idempotente (`updateOrCreate`/`firstOrCreate`) para evitar duplicidade.

1. Executar `php artisan migrate:fresh --seed`.
2. Se houver falha em migration:

- identificar migration quebrada pelo stack trace
- corrigir o arquivo de migration com alteração mínima necessária
- repetir `php artisan migrate:fresh --seed` até concluir sem erros.

1. Rodar `npm run build` para recompilar assets com o estado atualizado dos módulos.
2. Confirmar status final ao usuário:

- resultado do `migrate:fresh --seed`
- resultado do `npm run build`
- credenciais finais de login do admin fixo.

## Critérios de aceite

- `migrate:fresh --seed` finaliza 100% sem erro.
- `npm run build` finaliza com sucesso.
- Usuário `admin@jubaf.com.br` existe e tem role `Super Admin`.
- Entrega inclui dados de login solicitados.

# Plano 4 / Fase 4
## Fase1 Igrejas Usuarios
- overview: Implementar o alicerce da associação com CRUD de Igrejas, integração completa na gestão de usuários com vínculo de igreja + cargo Spatie, e navegação nos painéis Admin/Liderança.

- todos:
  - id: schema-igrejas-pastor
    content: Adicionar migration para pastor_titular e compatibilidade com lideranca_titular no model Igreja
    status: pending
  - id: crud-igrejas-controller-requests
    content: Criar AdminIgrejaController e Form Requests de store/update com upload de logo
    status: pending
  - id: routes-igrejas-admin-lideranca
    content: Configurar rotas protegidas admin.igrejas.* e lideranca.igrejas.* com can:gerenciar igrejas
    status: pending
  - id: views-igrejas-admin
    content: Criar views admin/index/form/create/edit premium para Igrejas
    status: pending
  - id: admin-usercontroller-integracao
    content: Atualizar UserController para carregar igrejas/roles e persistir igreja_id + syncRoles
    status: pending
  - id: admin-users-views-update
    content: Atualizar views de usuários com colunas Igreja/Cargo e selects de Igreja/Cargo
    status: pending
  - id: user-model-relationship
    content: Adicionar relacionamento igreja() no app/Models/User.php
    status: pending
  - id: sidebars-link-gestao-igrejas
    content: Adicionar link Gestão de Igrejas nas sidebars Admin e Liderança com @can
    status: pending
  - id: validacao-final-fase1
    content: Executar validações de rota, lints e fluxo funcional ponta a ponta
    status: pending

# Fase 1: Alicerce da Associação (Igrejas + Usuários)

## Objetivo

Construir o CRUD de Igrejas com UX premium e integrar o vínculo `igreja_id` + cargo (Spatie Roles) no fluxo de usuários, com acesso por permissão e navegação visível em Admin e Liderança.

## Decisões confirmadas

- Estratégia de campo pastor: **manter legado** `lideranca_titular` e **adicionar** `pastor_titular`.
- Escopo de rotas Igrejas: **Admin + Liderança**.

## Implementação proposta

### 1) Estrutura de dados e compatibilidade

- Criar migration para adicionar `pastor_titular` em `igrejas` (sem quebrar `lideranca_titular`).
- Atualizar `Modules\Igrejas\App\Models\Igreja` para aceitar ambos campos em `$fillable`.
- Definir prioridade de exibição nas views/listagens: `pastor_titular ?? lideranca_titular`.

Arquivos alvo:

- [Modules/Igrejas/database/migrations](../../../Users/Administrator/.cursor/plans/Modules/Igrejas/database/migrations)
- [Modules/Igrejas/app/Models/Igreja.php](../../../Users/Administrator/.cursor/plans/Modules/Igrejas/app/Models/Igreja.php)

### 2) CRUD completo de Igrejas (Admin + Liderança)

- Criar `AdminIgrejaController` com `index/create/store/edit/update/destroy`.
- Implementar upload de logo em `public/igrejas` com validação e substituição segura no update.
- Criar Form Requests:
  - `StoreIgrejaRequest`
  - `UpdateIgrejaRequest`
- Reestruturar rotas do módulo em grupos protegidos:
  - Admin: prefixo `admin/igrejas`, nomes `admin.igrejas.`, middleware `auth` + `can:gerenciar igrejas`.
  - Liderança: prefixo `lideranca/igrejas`, nomes `lideranca.igrejas.`, middleware equivalente + `can:gerenciar igrejas`.

Arquivos alvo:

- [Modules/Igrejas/app/Http/Controllers](../../../Users/Administrator/.cursor/plans/Modules/Igrejas/app/Http/Controllers)
- [Modules/Igrejas/app/Http/Requests](../../../Users/Administrator/.cursor/plans/Modules/Igrejas/app/Http/Requests)
- [Modules/Igrejas/routes/web.php](../../../Users/Administrator/.cursor/plans/Modules/Igrejas/routes/web.php)

### 3) Views premium do módulo Igrejas (Tailwind/Flowbite)

- Criar `admin/index.blade.php` com data table moderna:
  - Logo/avatar, Nome, Pastor, Líder de Jovens, Ações.
- Criar `admin/form.blade.php` reutilizável para create/edit:
  - Cards limpos, espaçamento premium, campos solicitados, preview de upload.
- Criar wrappers `create.blade.php` e `edit.blade.php` reutilizando o form.

Arquivos alvo:

- [Modules/Igrejas/resources/views/admin](../../../Users/Administrator/.cursor/plans/Modules/Igrejas/resources/views/admin)

### 4) Gestão de Usuários (Admin) com Igrejas + Cargo

- Em `Modules\Admin\App\Http\Controllers\UserController`:
  - `create/edit`: carregar `Igreja::orderBy('nome')->get()` e `Role::all()`.
  - `store/update`: persistir `igreja_id` e sincronizar role com `syncRoles($request->role)`.
- Ajustar validações de `store/update` para aceitar `igreja_id` e `role` (slug/nome de role) sem quebrar fluxo atual.
- Adicionar relação no `User` model:
  - `igreja(): belongsTo(Igreja::class, 'igreja_id')`.

Arquivos alvo:

- [Modules/Admin/app/Http/Controllers/UserController.php](../../../Users/Administrator/.cursor/plans/Modules/Admin/app/Http/Controllers/UserController.php)
- [app/Models/User.php](../../../Users/Administrator/.cursor/plans/app/Models/User.php)

### 5) Views de Usuário (Admin)

- `index.blade.php`:
  - adicionar coluna **Igreja** (`$user->igreja->nome ?? '-'`)
  - adicionar coluna **Cargo** (primeira role em badge Flowbite).
- `create/edit` (ou parcial de form):
  - select moderno para **Igreja Pertencente**
  - select moderno para **Cargo na JUBAF**.

Arquivos alvo:

- [Modules/Admin/resources/views/users/index.blade.php](../../../Users/Administrator/.cursor/plans/Modules/Admin/resources/views/users/index.blade.php)
- [Modules/Admin/resources/views/users/create.blade.php](../../../Users/Administrator/.cursor/plans/Modules/Admin/resources/views/users/create.blade.php)
- [Modules/Admin/resources/views/users/edit.blade.php](../../../Users/Administrator/.cursor/plans/Modules/Admin/resources/views/users/edit.blade.php)

### 6) Navegação (Admin + Liderança)

- Incluir link “Gestão de Igrejas” com ícone de igreja no padrão de ícones do projeto.
- Exibir apenas com `@can('gerenciar igrejas')`.
- Usar rotas por contexto:
  - Admin -> `admin.igrejas.index`
  - Liderança -> `lideranca.igrejas.index`

Arquivos alvo:

- [Modules/Admin/resources/views/components/sidebar.blade.php](../../../Users/Administrator/.cursor/plans/Modules/Admin/resources/views/components/sidebar.blade.php)
- [Modules/LiderancaPanel/resources/views/components/sidebar.blade.php](../../../Users/Administrator/.cursor/plans/Modules/LiderancaPanel/resources/views/components/sidebar.blade.php)

### 7) Verificação técnica

- Rodar checagens pós-implementação:
  - limpeza de cache quando necessário
  - `route:list` para confirmar nomes de rotas
  - validação de upload e CRUD ponta a ponta
  - leitura de lints nos arquivos alterados
- Confirmar cenários:
  - CRUD Igrejas Admin/Liderança
  - vínculo usuário-igreja
  - sincronização correta de role Spatie
  - visibilidade condicional em sidebars

## Critérios de aceite

- CRUD Igrejas funcional (Admin e Liderança) com validação e upload.
- Usuário com `igreja_id` persistido e role sincronizada por `syncRoles`.
- Listagem de usuários exibe Igreja e Cargo com badge.
- Sidebars exibem “Gestão de Igrejas” somente com `@can('gerenciar igrejas')`.
- Layout responsivo, limpo e consistente com Tailwind/Flowbite.

# Plano 5 / Fase 5
## Fase 2: Motor de Eventos e Caravanas (JUBAF)
- overview: Implementar a Fase 2 no módulo Events com jornada completa para Jovem, Líder Local e Diretoria, adicionando gestão de caravanas por igreja, ranking no admin e navegação dedicada nos painéis.

- todos:
  - id: memberpanel-events
    content: Ajustar fluxo MemberPanel (index, inscrições e my-registrations) com CTA/badges conforme Fase 2
    status: pending
  - id: lideranca-caravana-controller
    content: Criar CaravanaController com filtros por igreja_id e métricas de caravana
    status: pending
  - id: lideranca-caravana-views
    content: Criar views liderancapanel/caravanas (index e show) com Data Table e cards de resumo
    status: pending
  - id: admin-ranking-caravanas
    content: Adicionar query agregada e seção Ranking de Caravanas no show do evento (admin)
    status: pending
  - id: routes-and-sidebars
    content: Registrar rotas member/lideranca e atualizar sidebars com novos links e permissões
    status: pending
  - id: validation-pass
    content: Validar rotas, lints e consistência visual/funcional ponta a ponta
    status: pending


# Fase 2: Motor de Eventos e Caravanas (JUBAF)

## Objetivo funcional

Entregar um fluxo completo e alinhado ao propósito da JUBAF:

- Jovem: visualizar próximos eventos e acompanhar inscrições.
- Líder Local: acompanhar somente a caravana da sua igreja.
- Diretoria: visão macro com ranking de caravanas por igreja.

## Estratégia de implementação

### 1) MemberPanel: vitrine + inscrições + acompanhamento

- Atualizar o controller [Modules/Events/app/Http/Controllers/MemberPanel/EventController.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Events/app/Http/Controllers/MemberPanel/EventController.php):
  - `index`: manter filtro eficiente para eventos ativos/publicados e futuros (usar scopes existentes `published()`, `members()` e critério de data em `start_date/end_date`).
  - `myRegistrations` (compatível com `minhasInscricoes` solicitado): garantir eager loading de `event` (e `participants/latestPayment` quando necessário para badges/status).
  - `register`/`inscrever (POST)`: manter endpoint de inscrição já existente (`memberpanel.events.register`) com criação vinculada ao usuário logado e status inicial pendente, reaproveitando `EventService` para não quebrar o fluxo de pagamento atual.
- Refinar views já existentes para aderir ao layout pedido:
  - [Modules/Events/resources/views/memberpanel/index.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Events/resources/views/memberpanel/index.blade.php): cards premium com capa/título/data/local e CTA textual `Garantir Vaga`.
  - [Modules/Events/resources/views/memberpanel/my-registrations.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Events/resources/views/memberpanel/my-registrations.blade.php): formato de tickets e badges de pagamento (amarelo pendente, verde pago/confirmado).

### 2) Liderança: nova gestão de caravana por igreja

- Criar controller [Modules/Events/app/Http/Controllers/Lideranca/CaravanaController.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Events/app/Http/Controllers/Lideranca/CaravanaController.php):
  - `index`: eventos que tenham pelo menos 1 inscrição de usuários com `igreja_id` igual ao líder autenticado.
  - `show(Event $event)`: inscrições do evento filtradas por `user.igreja_id`, com eager loading (`user`, `batch`, `participants`, `latestPayment`) e métricas (`total_na_caravana`, `total_pago`).
- Criar views de liderança:
  - [Modules/Events/resources/views/liderancapanel/caravanas/index.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Events/resources/views/liderancapanel/caravanas/index.blade.php): grid/lista de eventos da caravana.
  - [Modules/Events/resources/views/liderancapanel/caravanas/show.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Events/resources/views/liderancapanel/caravanas/show.blade.php): datatable com colunas `Nome do Jovem`, `WhatsApp` (link direto), `Tipo de Ingresso`, `Status do Pagamento`, além de cards de resumo.

### 3) Admin: ranking de caravanas no detalhe do evento

- Atualizar [Modules/Events/app/Http/Controllers/Admin/EventController.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Events/app/Http/Controllers/Admin/EventController.php) no método `show`:
  - adicionar query agregada por igreja via `event_registrations -> users -> igrejas` com `COUNT(*)` ordenado desc.
  - entregar dataset `caravanaRanking` para a view.
- Atualizar [Modules/Events/resources/views/admin/events/show.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/Events/resources/views/admin/events/show.blade.php):
  - seção premium “Ranking de Caravanas” (tabela HTML/Tailwind/Flowbite), exibindo posição, igreja e total de inscritos.

### 4) Rotas e navegação

- Registrar rotas da caravana no escopo de liderança em [routes/lideranca.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/routes/lideranca.php) com padrão `lideranca.caravanas.*`.
- Ajustar rotas member (se necessário alias sem quebrar compatibilidade) em [routes/member.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/routes/member.php), preservando padrão `memberpanel.events.*` e endpoint de inscrições.
- Atualizar sidebars:
  - [Modules/MemberPanel/resources/views/components/sidebar.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/MemberPanel/resources/views/components/sidebar.blade.php): links `Próximos Eventos` e `Minhas Inscrições`.
  - [Modules/LiderancaPanel/resources/views/components/sidebar.blade.php](../../../Users/Administrator/.cursor/plans/C:/laragon/www/JUBAF/Modules/LiderancaPanel/resources/views/components/sidebar.blade.php): link `Minha Caravana` com `@can('acesso painel lideranca')`.

### 5) Validação técnica e UX

- Garantir queries com eager loading e filtros por igreja sem N+1.
- Manter consistência visual premium (cards, badges, spacing, estados vazios).
- Rodar validação de rotas e lint dos arquivos alterados para evitar regressão.

## Critérios de aceite

- Jovem vê eventos abertos e consegue se inscrever/acompanhar inscrições no painel.
- Líder Local visualiza apenas inscritos da própria igreja por evento (caravana).
- Admin vê ranking de caravanas por igreja no detalhe do evento.
- Sidebars e rotas novos funcionando com nomenclatura solicitada e permissões aplicadas.

# Plano 6 / Fase 6
