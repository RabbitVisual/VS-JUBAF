## Módulo Ministries – Visão Geral e Fluxos

O módulo `Ministries` é o **centro de gestão de ministérios e equipes** da igreja. Ele conecta liderança, planejamento, voluntariado, eventos, tesouraria, patrimônio (assets) e gamificação em um fluxo único, pensado para uso real em produção.

Este documento resume **como o módulo funciona hoje**, o que foi implementado e como usar os principais recursos na prática.

---

## 1. Domínio e Principais Entidades

- **Ministry**
  - Representa um ministério/equipe da igreja (por exemplo: Louvor, Intercessão, Ação Social).
  - Campos principais: `name`, `description`, `leader_id`, `co_leader_id`, `color`, `icon`, `is_active`, `max_members`, etc.
  - Relações importantes:
    - `leader`, `coLeader` → usuários responsáveis.
    - `members` (pivot `ministry_members`) → histórico de participação, função e datas (`joined_at`, `left_at`, `status`).
    - `plans()` → planos estratégicos (`MinistryPlan`).
    - `reports()` → relatórios mensais (`MinistryReport`).
    - Integrações por `ministry_id`:
      - `ebdClasses()` (EBD),
      - `worshipSetlists()` (Worship),
      - `socialCampaigns()` (Ação Social),
      - `prayerRequests()` (Intercessor),
      - eventos (`Events`) e reservas de equipamentos (`AssetReservation`).

- **MinistryPlan**
  - Representa o **plano estratégico** do ministério (anual/trimestral).
  - Campos principais:
    - Período (`period_year`, `period_type`, `period_start`, `period_end`).
    - Conteúdo (`objectives`, `goals` JSON, `activities` JSON).
    - Orçamento (`budget_requested`, `budget_notes`).
    - Status (`draft`, `under_council_review`, `approved`, `in_execution`, etc.).
    - Integração com o Conselho via `council_approval_id`.
  - Relações:
    - `ministry()`, `councilApproval()`, `reports()` (relatórios vinculados ao plano).

- **MinistryReport**
  - Relatório **mensal** do ministério.
  - Campos principais:
    - Período (`report_year`, `report_month`, `period_start`, `period_end`).
    - Conteúdo qualitativo (`qualitative_summary`, `highlights`, `challenges`, `prayer_requests`).
    - Conteúdo quantitativo (`quantitative_data` JSON, `treasury_summary` JSON).
    - Status (`draft`, `submitted`) e datas (`submitted_at`, `reviewed_at`).
  - Relações:
    - `ministry()`, `plan()`, `submitter()`, `reviewer()`.

- **MinistryServicePoint / Gamificação**
  - Registra pontos de serviço por relatório submetido, ligados ao usuário, ministério e relatório.
  - Integrado ao sistema de pontos global do usuário (gamificação).

---

## 2. Fluxos Principais (Ponta a Ponta)

### 2.1. Planejamento do Ministério

**Onde configurar (Admin):**
- Tela de detalhes do ministério em `admin/ministérios`:
  - Botão **“Novo plano”** → criação de `MinistryPlan`.
  - Listagem de planos e botão **“Enviar ao Conselho”** para aprovação.

**Ciclo:**
1. Admin/Líder define o plano no Admin (objetivos, metas, atividades, orçamento).
2. Envia para o Conselho (`CouncilApproval` tipo `ministry_plan`).
3. O Conselho aprova/reprova/solicita ajustes no módulo `ChurchCouncil`.
4. Ao aprovar, o plano entra em `in_execution` e pode gerar **eventos** (via `MinistryPlanEventService`).

### 2.2. Geração de Eventos a partir do Plano

- Na tela de **show do plano** (Admin), é possível:
  - Gerar **eventos individuais** a partir das atividades do plano.
  - Gerar **todos os eventos em lote**, já vinculando `ministry_id` e `ministry_plan_id`.
- Os eventos podem exigir aprovação do conselho (status `waiting_approval`) e alimentam:
  - O **console de eventos** (Events),
  - A **timeline de atividades** no painel do líder (MemberPanel).

### 2.3. Relatórios Mensais

- No MemberPanel, o líder acessa o ministério em `memberpanel.ministries.show`.
- Aba **[Planejamento]**:
  - Mostra o **plano em execução**.
  - Exibe o atalho para o **relatório do mês atual** (criar/editar/enviar).
- Tela de relatório (`memberpanel.ministries.reports.*`):
  - Wizard em 3 etapas (Conteúdo → Destaques → Desafios & Oração).
  - Opções:
    - **Salvar rascunho** (permite continuar depois),
    - **Enviar relatório** (marca como `submitted` e dispara integrações de gamificação/tesouraria).
- Aba **[Relatórios]**:
  - Linha do tempo dos últimos relatórios mensais, com status:
    - Verde → **Enviado**,
    - Amarelo → **Rascunho**.

### 2.4. Financeiro e Integração com Tesouraria

- Para líderes, o módulo consulta a `TreasuryApiService`:
  - Resumo financeiro do período atual (saldo, receitas, despesas) por `ministry_id`.
- Onde aparece:
  - Aba **[Dashboard]** → card “Saldo do mês”.
  - Aba **[Recursos / Assets]** → card “Caixa Ministério” com valores detalhados.
- O campo `treasury_summary` em relatórios mensais pode armazenar snapshots agregados, usados também no **PDF consolidado**.

### 2.5. Reservas de Equipamentos (Assets)

- Na aba **[Recursos / Assets]** do ministry (MemberPanel):
  - Card de “Equipamentos” com botão **“Solicitar equipamentos”**.
- Fluxo:
  1. Líder/co-líder abre o formulário de reserva (`memberpanel.ministries.reservations.create`).
  2. Seleciona **asset**, período (`start_at`, `end_at`) e, opcionalmente, um evento relacionado.
  3. Ao enviar, o sistema chama `AssetReservation::hasCollision()`:
     - Bloqueia sobreposição de horários para o mesmo asset em status `requested` ou `approved`.
  4. Admin acompanha/gerencia em `assets.admin.reservations.index` (sidebar Admin).

---

## 3. UI/UX – Navegação e Telas Principais

### 3.1. Sidebars e Acessos

- **Admin Sidebar (`Modules/Admin/resources/views/components/sidebar.blade.php`):**
  - Dentro de **“Conselho da Igreja”**:
    - **Visão Geral**
    - **Ministérios (Semáforo)** → `admin.churchcouncil.ministries.dashboard`
    - **Aprovações de Planos** → `admin.churchcouncil.approvals.index?type=ministry_plan`
    - **Reservas de Equipamentos** → `assets.admin.reservations.index`
  - Ícone principal do grupo: `<x-icon name="users-rectangle" />`.

- **MemberPanel Sidebar (`Modules/MemberPanel/resources/views/components/sidebar.blade.php`):**
  - Menu **“Meus Ministérios”**:
    - Ícone: `<x-icon name="church" />`.
    - Dropdown com `activeMinistries()` do usuário (líder/voluntário ativo).
    - Empty state:
      - Mensagem “Você ainda não participa de nenhum ministério.”
      - Link **“Ver todos os ministérios”** → `memberpanel.ministries.index`.

### 3.2. Tela do Ministério (MemberPanel – `memberpanel.show`)

Estrutura em **abas (pills)** com Alpine.js:

- **[Dashboard]**
  - Hero com:
    - Ícone do ministério (emoji ou fallback FA `church`),
    - Nome, descrição, cor temática,
    - Cards de liderança (Líder / Co-Líder) com avatar, email, badges.
  - Cards-resumo do líder:
    - **Voluntários ativos** (ícone `users`).
    - **Saldo do mês** (ícone `vault`, cores verde/vermelho).
    - **Status do relatório** com semáforo local (badge verde “Enviado” / amarelo “Pendente” e ícone `traffic-light`).
  - Card de vínculo (para qualquer membro):
    - Quando membro ativo → card “Vínculo Ativo” com função e data de entrada.
    - Quando não membro → card “Deseja ser parte?” com botão **“Participar Agora”**.

- **[Planejamento]**
  - Card **“Plano em execução”**:
    - Título, período, objetivos resumidos, orçamento planejado.
  - **Timeline de Atividades**:
    - Baseada nos eventos do ministério (`ministryEvents`).
    - Visual:
      - Linha vertical, pontos com `ring-blue-500`.
      - Cada item mostra título, data/hora e badge de status:
        - Verde → `published`,
        - Amarelo → `waiting_approval`,
        - Cinza → `closed` ou rascunho.
      - Link “Ver” para eventos publicados (rota `memberpanel.events.show`, quando disponível).
    - Empty state guiado quando não há eventos futuros.
  - Blocos de status do relatório do mês (atalho para criar/editar/enviar).

- **[Equipe]**
  - Quadro de voluntários ativos com cards individuais (foto ou inicial, nome, email, função).
  - Empty state com ícone FA e mensagem “Nenhum voluntário ativo listado ainda.”

- **[Recursos / Assets]**
  - Card de **Apoio Direto** (atalho para doação direcionada ao ministério).
  - Card **“Caixa Ministério”**:
    - Mostra saldo do mês, receitas e despesas, usando `TreasuryApiService`.
    - Empty state amigável se Tesouraria não estiver disponível ou sem permissão.
  - Card **“Equipamentos”**:
    - Breve descrição + botão **“Solicitar equipamentos”** → formulário de reserva.

- **[Relatórios]**
  - Timeline de relatórios mensais recentes (`recentReports`):
    - Mês/Ano (ex.: Março/2026),
    - Resumo qualitativo truncado,
    - Badge de status:
      - Verde (success) → enviado,
      - Amarelo (warning) → rascunho.
    - Para líderes, link “Abrir” → edição do relatório.
  - Empty state com ícone FA quando não há relatórios.

---

## 4. Telas Admin – Relatório Consolidado e Gestão

- **Tela do Ministério (Admin – `admin.ministries.show`)**:
  - Barra de ações:
    - **Novo plano**,
    - **Planos** (lista),
    - **Editar**,
    - **Relatório Consolidado (PDF)** → botão **primário azul** com ícone `file-pdf`:
      - `bg-blue-600 hover:bg-blue-700` e `focus:ring-blue-*`.
  - O botão chama `MinistryReportController@exportConsolidated`, que usa `PdfService` para renderizar view dedicada em PDF com:
    - Dados da liderança,
    - Plano atual,
    - Últimos 3 relatórios mensais,
    - Resumo financeiro da tesouraria.

- **Dashboard de Ministérios do Conselho (`admin.churchcouncil.ministries.dashboard`)**
  - Cards por ministério com:
    - Nome,
    - Líder atual,
    - Status de plano (Em execução, Em revisão, etc.).
  - **Semáforo de metas**:
    - Ponto de cor:
      - Verde → relatório do mês entregue,
      - Amarelo → pendente, mas dentro da tolerância,
      - Vermelho → sem relatório enviado no mês.
    - Tooltips explicam o significado de cada cor.

---

## 5. Carregamento e Feedback de UX

- Todos os layouts principais (Admin, MemberPanel, Ministries) incluem `<x-loading-overlay />`, que:
  - Mostra overlay automático em navegações e submits padrão (via listener `beforeunload`).
  - Pode ser disparado manualmente com:
    - `window.dispatchEvent(new CustomEvent('loading-overlay:show'))`
    - ou com mensagem: `window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }))`.
- Principais formulários relacionados ao módulo:
  - Plano estratégico (Admin),
  - Geração de eventos,
  - Relatório mensal (wizard),
  - Reserva de equipamentos (MemberPanel e Admin/Assets),
  - Já se beneficiam do overlay global quando submetidos de forma síncrona.

Mensagens de sucesso/erro são exibidas em alerts premium (bordas suaves, ícones FA de feedback) em todas as telas críticas (Ministry show, wizard de relatório, reservas).

---

## 6. Considerações para Produção

- **RBAC e Policies**
  - `MinistryPolicy` controla acesso a:
    - Visualizar ministérios,
    - Gerenciar membros,
    - Criar/editar planos,
    - Submeter relatórios.
  - Líderes/co-líderes têm acesso estendido no MemberPanel; admins e pastores via Admin.

- **Migrações e Integridade**
  - Todas as tabelas novas (`ministry_plans`, `ministry_reports`, `council_approvals` extendida, `asset_reservations`, etc.) usam FKs com `nullOnDelete` onde necessário.
  - Campos `ministry_id` adicionados em EBD, Worship, SocialAction e Intercessor permitem relatórios mais ricos e integração futura.

- **Desempenho**
  - Relações são carregadas com `with(...)` nos controllers para evitar N+1 (leader/coLeader, activeMembers, eventos, relatórios recentes).
  - Listas com potencial de crescimento têm limites razoáveis (por exemplo, eventos futuros limitados).

---

## 7. Como Evoluir

Com a fundação atual, as próximas evoluções naturais do módulo são:

- Painel analítico de ministérios (gráficos, comparativos por período).
- Exposição de métricas por API (`/api/v1/ministries/*`) para dashboards externos.
- Regras avançadas de gamificação vinculadas a metas cumpridas do plano.

O estado atual do módulo já está **pronto para produção**: visual coerente com o restante do sistema, navegação completa (Admin + MemberPanel), integrações ativas com Conselho, Tesouraria, Assets e gamificação, e relatórios consolidados em PDF para uso em reuniões e prestação de contas.

