# VertexCBAV Project - Agentic Context (Master Guide for Jules)

Welcome, Jules! This project is a specialized management system for Churches and Worship Teams (VertexCBAV). It is built with a premium aesthetic and enterprise-grade modular architecture.

## 🎯 Mandatory Development Philosophy

Your goal is to perform a full system upgrade ensuring consistency and a premium user experience, while strictly following these core principles:

1.  **Local & Internal First**:
    - **NEVER** use external APIs (Google, Bible APIs, etc.) if the resource exists locally.
    - **Bible Resources**: ALWAYS use `Modules/Bible`. It is the source of truth for scriptures.
    - **Worship Data**: Use `Modules/Worship` for chords, setlists, and academy data.
2.  **Low-Cost Live Features**:
    - **Avoid** infrastructure-heavy real-time solutions (like Reverb/WebSockets/Terminal processes) unless explicitly requested.
    - **JS-Centric Real-Time**: Prefer Alpine.js, Livewire Polling, or Vanilla JS to simulate "live" updates locally with low overhead.
3.  **Premium Aesthetic**: Keep the "Book-like", glassmorphic, and didactic design language. Professionalism is non-negotiable.

## 📂 Comprehensive Module Map

Understand the role of each module before making modifications:

- **Admin**: The nervous system. Handles User management, Roles/Permissions (Spatie), Global Settings, and Audit Logs.
- **Assets**: The inventory tracker. Manages physical church property and digital resources.
- **Bible**: **The Spiritual Core.** Contains multiple offline Bible versions. Use this for ANY scripture-related feature.
- **ChurchCouncil**: Leadership hub. Manages meeting agendas, minutes, decisions, and leadership history. Agenda status uses model constants (`pending`, `discussed`, `approved`, `rejected`, `postponed`); decision text (approve/reject) is stored in `CouncilAgenda.decision`. i18n via `churchcouncil::messages` (lang in `Modules/ChurchCouncil/lang`).
- **EBD**: The Vertex Academy. Sunday School management, gamification (XP/Levels), and student tracking.
- **Events Pro**: Event lifecycle. Handles calendar, public registrations, ticketing batches, and QR check-in flows.
- **HomePage**: Public CMS. Manages landing page sections, hero carousels, testimonials, and church info.
- **Intercessor**: Prayer Network. Moderates requests and tracks "prayer commitments" using low-cost JS updates.
- **MemberPanel**: Member Dashboard. Personalized interaction, profile management, and direct church engagement.
- **Ministries**: Organizational structure. Manages Music, Youth, Couples, and other specific church sectors.
- **Notifications**: Centralized alert engine. Handles in-app alerts and notifications across all modules. Use **InAppNotificationService** to send from any module (see below).
- **PaymentGateway**: Unified Donations. Securely integrates Mercado Pago, Stripe, and PIX (Manual/Auto).
- **Projection**: Live Sanctuary Media. Projects lyrics, bible verses, and service slides in real-time (JS-based).
- **Sermons**: Media Archive. A library for video and audio preached messages with categorization.
- **SocialAction**: Outreach Hub. Manages charity programs, "Smart Pantry" kits, and beneficiary dignity.
- **Treasury**: Financial Ledger. Manages church bookkeeping, budgeting, and detailed financial reports.
- **Worship**: Music & Liturgy. Repertoire management, setlists, and the Worship Academy (Academy/Chords).

## 🎨 Mandatory UI Standards

- **Icons**: Use ONLY **Font Awesome 7.1 Pro Duotone** via the `<x-icon name="icon-name" />` component.
- **Loading**: Every form submission must trigger the `<x-loading-overlay />`. The overlay is included once per layout (master) and shows after 200 ms on submit/beforeunload. To show it immediately (e.g. for async submits), dispatch `window.dispatchEvent(new CustomEvent('loading-overlay:show'))`; to show with a contextual message, use `window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Processando pagamento...' } }))`. To hide it programmatically, dispatch `window.dispatchEvent(new CustomEvent('stop-loading'))` or `loading-overlay:hide`. After ~12 s the overlay shows a "Fechar" option to avoid infinite spinner (UX best practice).
- **Skeleton (in-page loading)**: For sections that load via API (e.g. lists, cards), use `<x-skeleton variant="text|card|list" />` or pass custom content into the default slot. Prefer the full-screen `<x-loading-overlay />` for form/navigation loads.
- **Standards File**: **READ [system_default.md](file:///c:/xampp/htdocs/VertexCBAV/system_default.md)** before any major implementation.

## 🔔 In-App Notifications (InAppNotificationService)

To send in-app notifications (SystemNotification + UserNotification) from **any module**, inject and use `Modules\Notifications\App\Services\InAppNotificationService`:

- **sendToUser(User $user, string $title, string $message, array $options = [])** — Notify one user. Options: `type` (info|success|warning|error|achievement), `priority`, `action_url`, `action_text`, `broadcast`.
- **sendToAdmins(string $title, string $message, array $options = [])** — Notify all admin/lideranca users.
- **sendToUsers(Collection $users, string $title, string $message, array $options = [])** — Notify multiple users.
- **sendToRole(string $roleSlug, string $title, string $message, array $options = [])** — Notify users with a given role.

Example (in a controller or listener):

```php
use Modules\Notifications\App\Services\InAppNotificationService;

$this->inAppNotificationService->sendToUser($user, 'Título', 'Mensagem', [
    'type' => 'success',
    'action_url' => route('memberpanel.dashboard'),
    'action_text' => 'Ver painel',
]);
$this->inAppNotificationService->sendToAdmins('Alerta', 'Algo requer atenção.', ['priority' => 'high']);
```

Admin panel receives admin-targeted notifications; member panel receives member-targeted and anything the admin sends to them. Do not mix: target by role/user so the right panel shows the right list.

**API central de notificações (v1)** — Única API de notificações (sem rotas legadas). Igual à Bible API: serviço único e respostas com `{ data }`. Endpoints: `GET/POST/DELETE /api/v1/notifications/*` (middleware `web` + `auth`). Serviço: `Modules\Notifications\App\Services\NotificationApiService`. Controller: `Modules\Notifications\App\Http\Controllers\Api\V1\NotificationController`. Alimenta painéis, polling e qualquer cliente; o frontend (`resources/js/notifications.js`) usa somente esta API.

## 💳 PaymentGateway (config 100% Admin, API v1, webhook único)

Pagamentos são centralizados no módulo PaymentGateway. **Configuração 100% via Admin**: credenciais (Stripe, Mercado Pago, PIX) são gerenciadas apenas em **Admin > Gateways**; não usar variáveis de ambiente para chaves em produção (opcional só para seed em dev).

- **Webhook canônico (único)** — `POST /api/v1/gateway/webhook/{driver}` (ex.: `.../stripe`, `.../mercado_pago`). Configurar essa URL no painel de cada gateway. Não existem mais rotas legadas (`/payment/webhook`, `/checkout/webhook`).
- **Uso global** — Events, Donations, Treasury e futuros módulos devem usar apenas:
    - **PaymentGatewayFactory::make($driver)** para obter o driver (config vem do modelo PaymentGateway).
    - **PaymentService** para criar, processar e confirmar pagamentos.
    - **DonationPaymentService** para fluxo de doação (MemberPanel e Public): validação, payer, payable e chamada a PaymentService.
- **API v1** — `GET /api/v1/payment-gateways` (gateways ativos para frontend, sem credenciais); `GET /api/v1/payments/status?transaction_id=xxx` ou `GET /api/v1/payments/{transactionId}/status` (status para polling). Formato de resposta: `{ data }`. Serviço: `PaymentGatewayApiService`; controller: `Modules\PaymentGateway\App\Http\Controllers\Api\V1\PaymentGatewayController`.
- **Como outros módulos criam pagamentos** — Criar registro via `PaymentService::createPayment($data)` com `payment_gateway_id`, `payment_type`, `amount`, etc.; em seguida `PaymentService::process($payment)` ou `processPaymentBrick($payment, $brickData)` conforme o método (Checkout, Brick, PIX).
- **Como adicionar um novo gateway** — (1) Implementar `PaymentGatewayInterface` em um novo Driver; (2) Registrar o driver no `PaymentGatewayFactory::make()` (match pelo nome); (3) Adicionar campos de credenciais no formulário Admin `admin/gateways/edit.blade.php` e (4) adicionar branch em `GatewayWebhookController::handle()` para o novo driver. Exibir a URL do webhook na tela de edição do gateway (como já feito para Stripe e Mercado Pago).

## Treasury (API central v1, controle fiscal, relatórios, prestação de contas)

Controle fiscal, receitas, despesas, campanhas, metas financeiras, relatórios e prestação de contas. Suporte à formalização legal e auditoria (declaração de renda, governo). Sem rotas legadas; consumidores devem usar apenas `/api/v1/treasury/*`.

- **API v1** — Formato `{ data }`. Prefixo `GET/POST/PUT/DELETE /api/v1/treasury/*` (middleware `web` + `auth`). Endpoints: `dashboard`, `entries` (list, show, store, update, destroy), `entries/import-payment/{paymentId}`, `campaigns`, `goals`, `reports` (agregados por período), `permissions`, `entry-form-options`. Serviço: `Modules\Treasury\App\Services\TreasuryApiService`. Controller: `Modules\Treasury\App\Http\Controllers\Api\V1\TreasuryController`.
- **Rotas web** — Admin: módulo (prefix `treasury`), em `Modules\Treasury\routes\web.php`. Member: `routes/member.php` (prefix `tesouraria`, nome `memberpanel.treasury.*`), proxy para controllers Admin. Exportações Excel/PDF permanecem via web (ReportController).
- **Listeners** — **HandlePaymentReceived** (PaymentGateway): cria FinancialEntry ao concluir pagamento; não cria para `payment_type === event_registration` (deixado para RegistrationConfirmed). **RegistrationConfirmedListener** (Events): único criador de entrada para inscrições confirmadas; referência `REG-{id}` e idempotência; `Events\CreateFinancialEntry` é no-op (deprecated).
- **Integrações** — PaymentGateway (Payment → FinancialEntry, Campaign como payable); Events (RegistrationConfirmed → FinancialEntry); HomePage (Campaign::active()); Ministries (FinancialEntry.ministry_id). Permissões: `TreasuryPermission` (canViewReports, canCreateEntries, canEditEntries, canDeleteEntries, canManageCampaigns, canManageGoals, canExportData, isAdmin).

## Events (lógica única de preço e formulário, um CRUD admin, fluxo unificado)

Ciclo de vida de eventos: calendário, inscrições públicas e de membros, lotes, check-in e integração com PaymentGateway/Treasury.

- **Lógica única de preço** — `EventService::calculateRegistrationTotal()`: com lote, base = preço do lote e no máximo uma regra de nível inscrição (código, early bird, último minuto, etc.) por prioridade; sem lote, soma por participante da primeira regra que der match. Regras em `event_price_rules`; uma única seção "Regras de preço" no admin (create/edit). Mesma lógica em admin, público e member.
- **Formulário único** — `form_fields` (JSON) no evento: tipo, label, name, required, options (para select/radio). Definido uma vez no admin (create/edit); mesma estrutura e validação na inscrição pública e no painel do membro; respostas em `custom_responses` por participante.
- **Um CRUD admin** — Apenas `admin/events` (prefix `admin.events.`). Controller: `Modules\Events\App\Http\Controllers\Admin\EventController`. Rotas: resource events, duplicate, batches (store/update/destroy), checkin (index, validate), registrations (index, show, confirm, cancel, export pdf/badges/excel). Não existe mais `admin/events-v2`.
- **Evento configurável** — Coluna `events.options` (JSON): `has_badge`, `has_certificate`, `has_checkin`, `has_ticket`, `show_schedule`, `show_speakers`. Blocos de crachá e certificado no edit só aparecem quando o recurso está ativo.
- **Tipos de evento** — `EventTypesSeeder`: Assembleia, Congresso, Culto, Curso, Workshop, Louvores, Seminário, Conferência, Retiro, Mentoria, Outro. Filtro e dropdown no admin.
- **Fluxo único de inscrição** — Entrada: `eventos/{slug}`. CTA "Inscrever-se"/"Comprar" leva a `eventos/{slug}/inscrever` (formulário único: lotes se houver, participantes, form_fields). Pago → criação da inscrição e redirecionamento para `eventos/inscricao/{uuid}/pagar`; grátis → confirmação direta. Redirecionamentos 302: `eventos-v2/{slug}/checkout` → `eventos/{slug}/inscrever`; `eventos-v2/checkout/confirmation/{uuid}` → `eventos/inscricao/{uuid}/pagar`; `eventos-v2/ticket/{uuid}/download` → `eventos/inscricao/{uuid}/ingresso`.
- **Permissões** — `EventPolicy` (viewAny, view, create, update, delete, manageRegistrations, checkin, export). Registrada em `EventsServiceProvider`; todas delegam a `hasAdminAccess()` até eventual uso de permissões granulares. Sidebar admin: "Eventos" e "Check-in" visíveis com `@can('viewAny', Event::class)` e `@can('checkin', Event::class)`.
- **Integrações** — `ministry_id` (nullable) e `setlist_id` (nullable) em `events`; dropdowns no create/edit quando Ministries e Worship estão disponíveis. PaymentGateway e Treasury: confirmação de pagamento e criação de entrada financeira via listeners.

## Worship (API central v1, setlists, músicas, slides, Academy)

Dados de louvor (setlists, músicas, ChordPro, Academy) são servidos por uma **única API** em `/api/v1/worship/*`. Formato de resposta: `{ data }`. Sem rotas legadas: não usar `worship/api` nem proxies de setlists no Projection; todo consumo de setlists/músicas/slides é via API v1.

- **Endpoints** — `GET /api/v1/worship/setlists`, `GET /api/v1/worship/setlists/{id}` (setlist com items e slides), `GET /api/v1/worship/songs?q=`, `GET /api/v1/worship/songs/{id}`, `GET /api/v1/worship/songs/{id}/slides` (slides para projeção). Academy (auth): `GET/POST /api/v1/worship/academy/courses`, `GET /api/v1/worship/academy/courses/{id}` (classroom), `GET /api/v1/worship/academy/courses/{id}/structure` (admin builder), `POST /api/v1/worship/academy/courses/{id}/structure`, `POST /api/v1/worship/academy/lessons/{id}/complete`.
- **Serviço** — `Modules\Worship\App\Services\WorshipApiService` (getSetlists, getSetlistWithItems, getSongSlides, searchSongs, getAcademyCourses, getAcademyCourseWithProgress, markLessonComplete, getAcademyCourseStructure, createAcademyCourse, updateAcademyCourseStructure). Controller: `Modules\Worship\App\Http\Controllers\Api\V1\WorshipController`.
- **Consumidores** — **Projection**: WorshipTab.vue consome apenas `/api/v1/worship/setlists` e `/api/v1/worship/setlists/{id}` (constante `WORSHIP_API`); não há rotas legadas de setlists no Projection. Sermons (dropdown de músicas pode usar `GET /api/v1/worship/songs?q=`); Admin e MemberPanel (Academy usam `/api/v1/worship/academy/*`). Lógica ChordPro→slides fica só no Worship (WorshipApiService::getSongSlides); Projection não implementa parseSongLyrics.
- **Views MemberPanel Worship** — `academy/index.blade.php` (lista de cursos); `academy/classroom.blade.php` (container Vue Classroom, dados via API v1); `academy/course.blade.php` (não usada hoje: rota `academy.course` redireciona para classroom). Rehearsal: `rehearsal/index.blade.php`, `rehearsal/show.blade.php`. Stage: `stage/viewer.blade.php`. Minhas escalas: `my-rosters/index.blade.php`.
- **Criar curso (Admin)** — View `admin/academy/courses/create.blade.php` monta o componente Vue `CourseCreator`; `@vite(['Modules/Worship/resources/assets/js/app.js'])`; o Vue chama `POST /api/v1/worship/academy/courses` e redireciona para `/admin/worship/academy/courses/{id}`.
- **Academy** — Usar modelos `AcademyCourse`, `AcademyLesson`, `AcademyProgress` (v2). `WorshipAcademyCourse` e `WorshipAcademyLesson` estão deprecados (mantidos para compatibilidade; `WorshipMusicianProgress` e testes ainda referenciam).
- **Admin Academy (web)** — Rotas em `routes/admin.php`: listagem, criar, editar, ver, **atualizar** (`PUT courses/{id}`), **excluir** (`DELETE courses/{id}`), storeLesson, updateLesson, destroyLesson, builder. Controller: `AcademyAdminController`. Member classroom: `AcademyMemberController::classroom` + frontend consome apenas `/api/v1/worship/academy/*`.
- **Código removido (confirmado morto)** — `AcademyCourseController` (Worship), `ClassroomController` (Worship MemberPanel) e `TransposeApiController` não estavam registrados em nenhuma rota; transposição é feita no front (rehearsal/stage com JS). Nenhuma view ou JS chamava esses controllers.
- **Novos endpoints** — Implementar no WorshipApiService, expor no WorshipController (Api\V1) e registrar em `routes/api.php` no grupo `v1/worship`.
- **Import ChordPro / OpenSong** — Admin: `GET worship/songs/import`, `POST worship/songs/import-chordpro`, `POST worship/songs/import-opensong`. Serviços: `ChordProImporter`, `OpenSongImporter`; criam `WorshipSong` com `content_chordpro` e `lyrics_only`.

## Projection (API v1, tela ao vivo, remote, console)

Projeção de letras, versículos e mídia no culto. **Única API**: `GET/POST /api/v1/projection/*`. Não existem rotas legadas `projection/api`; todo consumo é via API v1.

- **Fontes de dados** — Worship (setlists, itens, slides ChordPro via `getSetlistWithItems` / `getSongSlides`), Bible (versos na aba Bíblia do console), Projection (state em cache, temas, assets, custom slides).
- **Fluxo** — Console (Vue) e Remote (Alpine) atualizam o state via `POST /api/v1/projection/state`; a tela de projeção (Screen) faz polling em `GET /api/v1/projection/state` e exibe slide/clear/media/countdown/logo/alertas. Teclado no console: setas = slide, Ctrl+setas = item (estilo Quelea).
- **Endpoints** — state (GET/POST), state/next-slide, state/prev-slide, assets (GET/POST/DELETE), assets/serve/{filename} (público, throttle), slides, lyrics/search, timeline/items (POST/DELETE), timeline/reorder, events/upcoming, themes (CRUD), card-templates (GET). Respostas sempre `{ data }`.
- **Tela em dispositivo separado** — **Admin > Projeção > Configurações**: ativar "Tela sem login (viewer token)" e definir um token. A tela usa `GET /api/v1/projection/viewer/state?viewer_token=XXX` sem login (throttle 60/min). Fallback: `PROJECTION_VIEWER_TOKEN` no `.env` quando a opção está ativa e o token não foi definido no painel.

## 🛠 Setup & Verification

- Keep `npm run dev` active for Tailwind 4.1 compilation.
- Always use `php artisan module:list` to verify active modules.
- Ensure cross-module synergy (e.g., `Worship` talking to `Projection`).

## 🌱 Seeds & Dados de Demonstração (sem migrate:fresh)

NUNCA usar `migrate:fresh` em ambientes com dados reais. Toda a estratégia de seed foi pensada para ser **idempotente** e segura para reexecutar em desenvolvimento.

- **Seeder raiz**: `database/seeders/DatabaseSeeder.php`
    - Sempre executa (qualquer ambiente):
        - Usuário de teste idempotente (`test@example.com`);
        - `PaymentGatewayDatabaseSeeder` (gateways padrão);
        - `TreasuryDatabaseSeeder` (campanhas/metas + lançamentos de exemplo uma única vez);
        - `EventsDatabaseSeeder` (evento de exemplo + tipos).
    - Apenas em ambiente `local`/`development`/`dev`:
        - Chama `LocalDemoSeeder` com todos os módulos de demonstração.

- **Seeder de demo local**: `database/seeders/LocalDemoSeeder.php`
    - Responsável por popular dados de teste em:
        - `Modules/Worship` (instruments, songs, Worship Academy);
        - `Modules/EBD` (classes, lições, jogos, XP rules, achievements, ano letivo consolidado);
        - `Modules/HomePage` (testemunhos, eventos vitrine, galeria) — só insere se as tabelas estiverem vazias;
        - `Modules/Gamification` (insights, coaching rules, page insights);
        - `Modules/Treasury` (reutiliza o seeder; não recria lançamentos se já existirem);
        - `Modules/Events`, `ChurchCouncil`, `SocialAction`, `Intercessor`, `Sermons`, `Ministries`, `Assets`, `Projection`, `MemberPanel`, `Admin`, `Notifications`, `Bible`.

- **Comandos recomendados**:
    - **Ambiente local/homologação (para encher tudo de dados demo)**:
        ```bash
        php artisan migrate
        php artisan db:seed
        ```
        Isso garante:
        - Módulos Worship/EBD/Events/HomePage/Treasury/etc. populados para testes;
        - Nenhuma tabela é dropada; seeds evitam duplicar grandes volumes (Tesouraria) e só criam vitrine da HomePage se as tabelas estiverem vazias.
    - **Para reseedar apenas um módulo específico (local)**:
        ```bash
        php artisan db:seed --class="Modules\\Worship\\Database\\Seeders\\WorshipDatabaseSeeder"
        php artisan db:seed --class="Modules\\EBD\\Database\\Seeders\\EBDDatabaseSeeder"
        php artisan db:seed --class="Modules\\Events\\Database\\Seeders\\EventsDatabaseSeeder"
        ```
        (ou `php artisan module:seed Worship` se os módulos estiverem registrados conforme o padrão do Laravel Modules).
    - **Produção**:
        - Evitar `db:seed` após ter dados reais. Se realmente necessário, rodar apenas seeders pontuais (ex.: `EventTypesSeeder`) e sempre revisar o código do seeder antes.

Com isso, para testes basta `php artisan db:seed` em ambiente local, sem risco de perder dados existentes e sem necessidade de `migrate:fresh`.

Go ahead, Jules, and make this project the best church management system ever built!

## Cursor Cloud specific instructions

### System dependencies (pre-installed in VM snapshot)

- PHP 8.2 with extensions: pdo_mysql, gd, mbstring, xml, zip, bcmath, intl, sqlite3, curl (via `ppa:ondrej/php`)
- Composer 2.x (`/usr/local/bin/composer`)
- MySQL 8.0 (Ubuntu package)
- Node.js 22.x + npm (pre-installed via nvm)

### Starting MySQL

MySQL must be started manually in the Cloud VM before running the app:

```
sudo mysqld_safe &
sleep 3
```

Root access is passwordless (`sudo mysql`). The database `vertex_cbav_db` is created during first setup; `php artisan migrate` handles schema.

### Running the application

Standard commands per `composer.json`:

- **Dev server (all services):** `composer dev` — starts `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, and `npm run dev` via concurrently.
- **Individually:** `php artisan serve --host=0.0.0.0 --port=8000` + `npm run dev` (Vite on port 5173).
- **Build assets:** `npm run build` (required before `php artisan test` since tests hit views that reference the Vite manifest).

### Running tests

- Core tests: `php artisan test` (7 tests, all pass). Uses SQLite `:memory:` per `phpunit.xml`.
- Module tests: `./vendor/bin/phpunit Modules/*/tests` — 4 of 14 module tests have pre-existing failures (missing controller class, dropped EBD tables not reflected in sidebar queries). These are codebase issues, not environment issues.
- Always run `npm run build` before testing to generate `public/build/manifest.json`.

### Linting

- `./vendor/bin/pint --test` — reports style issues but does not modify files. Existing codebase has many Pint violations (pre-existing).

### Demo credentials (from seeders)

- Admin: `admin@demo.com` / `admin123`
- Member: `membro@demo.com` / `membro123`

### Known gotchas

- The migration `2026_01_22_023422_drop_ebd_tables` may fail on fresh `migrate` if the `ebd_lesson_media` table (created by a later migration) was already created — it has a FK to `ebd_lessons`. Workaround: drop `ebd_lesson_media` first with `SET FOREIGN_KEY_CHECKS=0` in MySQL, then re-run `php artisan migrate`.
- The admin dashboard controller queries `ebd_classes`/`ebd_students` tables that were dropped. The dashboard still loads but may show errors in dev logs related to these missing tables. This is a pre-existing codebase issue.
- Session driver is `database` — `sessions` table is created via migration. If you get session errors, ensure migrations have run.
