# Treasury CBAV2026 – Padrão Batista Completo

Resumo das funcionalidades e integrações do módulo Tesouraria após o alinhamento ao padrão batista (CBAV2026).

## Segurança e auditoria

- **Transações**: Todas as escritas financeiras (criar/atualizar/excluir entrada, importar pagamento) são executadas dentro de `DB::transaction()` no `TreasuryApiService`.
- **Log imutável**: Tabela `audit_financial_logs` (append-only) registra ação (`created`/`updated`/`deleted`/`reversed`), modelo, id, `old_values`, `new_values`, `user_id` e `ip`. Trait `AuditableTransaction::logAudit()` é chamada pelo serviço após cada operação.
- **Exclusão**: Apenas soft delete em `FinancialEntry`. Não há `forceDelete` em fluxos de tesouraria. Preferir estorno (lançamento contrário); coluna `reversal_of_id` permite vincular entrada de estorno à original.

## Plano de contas e categorias

- **Tabela `financial_categories`**: Categorias pré-definidas (receitas e despesas) com `type`, `slug`, `name`, `is_system`, `order`. Seeder `TreasuryCategoriesSeeder` popula o plano padrão batista.
- **Receitas**: Dízimos, Ofertas Alçadas, Ofertas de Missões (Nacional/Estadual/Mundial), Fundo de Construção, Doações, Campanha, Outros.
- **Despesas**: Preletores, Manutenção, Ação Social, Educação Cristã, Salários e Encargos, Contas/Utilidades, Equipamentos, Eventos, Contribuição Denominacional, Outros.
- **Entradas**: `financial_entries.category_id` (FK) e `category` (enum legado) preenchidos pelo serviço a partir do slug da categoria.

## Identificação de dízimo (member_id)

- Coluna `member_id` (nullable) em `financial_entries` para vincular receita ao membro (ex.: dízimo identificado para recibo).
- Acesso a dados por `member_id` restrito por permissão (apenas perfis com permissão adequada ou o próprio membro para seu recibo).

## Despesas e fluxo de aprovação

- **expense_status**: Coluna em `financial_entries` com valores `pending`, `approved`, `paid`. Ao criar despesa, inicia como `pending`. Quando o conselho aprova (ChurchCouncil), o status é atualizado para `approved` em `executeFinancialRequestApproval`.
- **Integração ChurchCouncil**: Despesa acima de `church_council_auto_approve_budget_limit` gera `CouncilApproval` (tipo `financial_request`). Ao aprovar, além de `council_approved_at`, a entrada recebe `expense_status = approved`.

## Centro de custos (fundos)

- **Tabela `financial_funds`**: Nome, slug, descrição, `is_restricted`. Seeder `TreasuryFundsSeeder` cria Caixa Geral, Fundo de Missões, Fundo de Construção.
- **financial_entries.fund_id**: Opcional; sem fundo = caixa geral.

## Plano Cooperativo

- **Configuração**: Settings `treasury_plano_cooperativo_percent` (ex.: 10) e `treasury_plano_cooperativo_base` (`tithes_only` | `tithes_offerings` | `total_income`).
- **Relatório**: Bloco "Plano Cooperativo" na tela de relatórios mostra percentual, base do período e valor sugerido a repassar. Categoria de despesa "Contribuição Denominacional" para lançamentos manuais.

## Relatórios e transparência

- **Balancete mensal**: PDF gerado para o período; quando o período cobre exatamente um mês civil, o título do PDF é "Balancete Mensal" (para assembleia).
- **Dashboard**: Mantido; agrupamentos por categoria usam `financial_categories` quando disponível.
- **Comprovante anual de contribuição**: Rota `GET treasury/reports/contribution-receipt?member_id=&year=`. Emite PDF com totais por categoria para o membro no ano. Membro pode emitir apenas o próprio; tesoureiro/pastor (canExportData) pode emitir de qualquer membro.

## Integrações

- **PaymentGateway**: Pagamentos importados via `TreasuryApiService::importPayment`; entrada criada com `createEntry` (transação + auditoria); `category_id` e categorias legadas mapeados.
- **Events**: `RegistrationConfirmedListener` cria entrada para inscrição confirmada.
- **ChurchCouncil**: Despesa acima do limite gera aprovação; ao aprovar, `expense_status = approved` na entrada.
- **Ministries**: `ministry_id` em entradas mantido.
- **HomePage**: Campanhas ativas para vitrine.

## API v1

- Endpoints em `/api/v1/treasury/*` (dashboard, entries, campaigns, goals, reports, permissions, entry-form-options). `getEntryFormOptions()` inclui `financial_categories` e `financial_funds`.
- Formulários admin (create/edit de entradas) usam categorias e fundos do serviço; despesas exibem campo `expense_status` na edição.

## Precisão monetária

- Valores armazenados em `decimal(15,2)`; modelos com cast `decimal:2`. Cálculos no serviço devem evitar float (usar bcmath ou string quando necessário).
