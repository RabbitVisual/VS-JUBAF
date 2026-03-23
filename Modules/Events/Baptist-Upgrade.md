# Events Module – Baptist Upgrade (Documentação)

Documentação do módulo de **Eventos** do VertexCBAV após o upgrade batista: fluxos canônicos, regras de negócio, integrações e configuração.

---

## 1. Visão geral

O módulo Events é a plataforma de eventos cristãos da igreja: inscrições públicas e de membros, lotes, segmentos por faixa etária/papel, cupons, pagamento via PaymentGateway e lançamento na Tesouraria. Tudo alinhado à eclesiologia batista (sem categorias VIP/camarote; segmentos para faixas etárias e papéis ministeriais).

---

## 2. Fluxos canônicos

### 2.1 Público (visitantes e membros não logados)

- **Entrada**: `/eventos` (listagem) ou `/eventos/{slug}` (detalhe) ou `/eventos/{slug}/landing` (página de divulgação).
- **Inscrição**: Toda inscrição pública passa pelo wizard único em `eventos/{slug}/inscrever` (ou modal na landing). Redirecionamentos legados:
    - `eventos-v2/{slug}/checkout` → `eventos/{slug}/landing?openRegistration=1`
    - `eventos-v2/checkout/confirmation/{uuid}` → `eventos/inscricao/{uuid}/pagar`
    - `eventos-v2/ticket/{uuid}/download` → `eventos/inscricao/{uuid}/ingresso`
- **Eventos visíveis**: Apenas eventos com `visibility` in `public` ou `both` e `status = published`.

### 2.2 Painel de membros (MemberPanel)

- **Entrada**: `painel/eventos` (listagem) e `painel/eventos/minhas-inscricoes` (minhas inscrições).
- **Eventos visíveis**: Apenas eventos com `visibility` in `members` ou `both`. Eventos somente públicos não aparecem na listagem do painel; acesso direto por URL retorna 404.
- **Dados do membro**: Nome, e-mail, data de nascimento, CPF e telefone são pré-preenchidos a partir do perfil do usuário logado.
- **Pagamento**: Mesma lógica do público (PaymentService, gateways ativos). Inscrições gratuitas são confirmadas na hora.

### 2.3 Admin

- **CRUD único**: `admin/events/events` (prefixo `admin.events.events.*`). Não existe mais `admin/events-v2`.
- **Abas do editor**: Geral (título, tipo, datas, local, capacidade, opções), Status e visibilidade, Integrações (campanha tesouraria, aprovação conselho, destaque na home), Inscrição e vagas (formulário único ou segmentos), Lotes e preços, Cupons, Certificado/Crachá, Palestrantes.
- **Filtros na listagem**: Status, visibilidade, tipo, busca por texto, período (data de / data até).
- **Permissões**: `EventPolicy` (viewAny, view, create, update, delete, manageRegistrations, checkin, export). Sidebar: "Eventos" e "Check-in" conforme policy.

---

## 3. Regras de negócio principais

### 3.1 Tipos e visibilidade

- **Tipos**: Fonte única `EventTypesSeeder` (Retiro, Congresso, Culto, Curso, Workshop, Louvores, Conferência, etc.). Filtro por tipo no admin e no público.
- **Visibilidade**:
    - `public`: só listagem pública e home.
    - `members`: só painel de membros.
    - `both`: público e painel de membros.

### 3.2 Inscrição e vagas

- **Modo único**: Formulário único (um segmento implícito) ou **por faixa/categoria** (vários `EventRegistrationSegment`: ex. Crianças, Adolescentes, Jovens, Casais).
- **Segmentos**: rótulo, idade mínima/máxima, capacidade opcional, pede telefone, documentos, `form_fields` por segmento. Sem categorias de privilégio econômico (VIP/camarote).
- **Preço**: Motor único em `event_price_rules` (evento, segmento, lote). Cálculo centralizado em `EventService::calculateRegistrationTotal()` (base, regras, cupom).

### 3.3 Cupons

- **Modelo**: `EventCoupon` (código, tipo percent/fixed, valor, max_uses, max_uses_per_user, datas, ativo).
- **Validação**: Não permitir em eventos 100% gratuitos; não combinar dois cupons; respeitar uso global e por usuário.
- **Admin**: CRUD de cupons na tela de edição do evento (seção Cupons).

### 3.4 Aprovação conciliar

- **Campo**: `requires_diretoria_approval` (boolean). Quando ativo e o evento é publicado, é criado um `diretoriaApproval` (Diretoria) e o evento permanece em `draft` até aprovação.
- **Uso**: Eventos que implicam grandes gastos, mudanças ou assembleias.

---

## 4. Integrações

### 4.1 PaymentGateway

- Todas as cobranças de eventos usam `PaymentService::createPayment` com `payment_type = 'event_registration'` e `payable` = `EventRegistration`.
- Webhook canônico: `POST /api/v1/gateway/webhook/{driver}`. Ao concluir pagamento, o listener `ConfirmRegistrationOnPaymentCompleted` confirma a inscrição e dispara `RegistrationConfirmed`.

### 4.2 Treasury

- **Única origem de entrada financeira para inscrições**: `Treasury\RegistrationConfirmedListener` (escuta `RegistrationConfirmed`). Cria `FinancialEntry` com referência `REG-{id}` (idempotente). O listener `Events\CreateFinancialEntry` está deprecated (no-op).
- **Campanha opcional**: No evento pode ser definido `treasury_campaign_id`. Se definido, o `FinancialEntry` é criado com `campaign_id` igual a essa campanha.

### 4.3 HomePage

- **Vitrine**: Eventos em destaque na home vêm de `Event::upcoming()->public()->orderBy('is_featured', 'desc')->orderBy('start_date')`. Apenas eventos visíveis ao público (`public` ou `both`). Flag **Destacar na Home** = `is_featured` (configurável no admin na seção Integrações).

### 4.4 Notifications

- **Capacidade**: Ao confirmar uma inscrição, o listener `NotifyAdminsOnEventCapacity` verifica se o evento atingiu ≥ 80% da capacidade; em caso positivo, notifica os admins via `InAppNotificationService::sendToAdmins` (com link para o evento).
- **Confirmação ao participante**: Já tratada pelo módulo Notifications (template de e-mail e notificação in-app conforme configuração).

---

## 5. Estados da inscrição

- `pending`: Criada, aguardando pagamento ou confirmação manual.
- `awaiting_payment`: Com pagamento criado, aguardando conclusão.
- `confirmed`: Paga e confirmada (dispara `RegistrationConfirmed` e gera entrada na tesouraria quando pago).
- `cancelled`: Cancelada (admin ou sistema).
- `expired`: Expirada (ex.: prazo do lote).

Transições e idempotência (criação de inscrição e pagamento por uuid) devem ser respeitadas pelos controladores e listeners.

---

## 6. Configuração de um evento completo (resumo)

1. **Admin > Eventos > Criar**: Preencher geral, datas, local, capacidade, status, visibilidade, tipo.
2. **Integrações**: Opcionalmente campanha tesouraria, exige aprovação conselho, destaque na home.
3. **Inscrição e vagas**: Escolher formulário único ou segmentos; definir faixas etárias e quantidades.
4. **Lotes e preços**: Se usar lotes, criar com nome, preço, quantidade e janela de vendas.
5. **Regras de preço**: Globais e/ou por segmento (faixa etária, early bird, código, etc.).
6. **Cupons**: Criar códigos com desconto e limites de uso.
7. Publicar (ou enviar para aprovação do conselho, se exigido).

---

## 7. Limpeza e rotas legadas

- Rotas `eventos-v2/*` redirecionam para o fluxo unificado (`eventos/*` e landing). Nenhum controller ou rota duplicada de eventos no admin; único CRUD em `Admin\EventController`.
- JS e views que referenciassem apenas `events-v2` foram removidos ou atualizados para o fluxo canônico.

---

_Documento gerado no âmbito do plano Baptist Events Module – Full Upgrade._
