# Módulo Events - Sistema de Gestão de Eventos

## Visão Geral

Módulo completo para gestão de eventos da igreja, com sistema de inscrições, precificação dinâmica por idade, integração com pagamentos e tesouraria.

## Estrutura Criada

### 1. Migrations ✅

-   `create_events_table.php` - Tabela principal de eventos
-   `create_event_price_rules_table.php` - Regras de precificação por idade
-   `create_registrations_table.php` - Inscrições (carrinho/pedido)
-   `create_participants_table.php` - Participantes (dados dos inscritos)

### 2. Models ✅

-   `Event.php` - Model principal com relacionamentos
-   `EventPriceRule.php` - Regras de preço
-   `Registration.php` - Inscrições
-   `Participant.php` - Participantes

### 3. Services ✅

-   `EventService.php` - Lógica de negócio:
    -   `calculateRegistrationTotal()` - Calcula valor total da inscrição
    -   `calculatePriceForAge()` - Calcula preço por idade
    -   `validateCapacity()` - Valida capacidade do evento
    -   `createRegistration()` - Cria inscrição com participantes
    -   `confirmRegistration()` - Confirma inscrição (após pagamento)
    -   `cancelRegistration()` - Cancela inscrição

### 4. Events e Listeners ✅

-   `RegistrationConfirmed.php` - Evento disparado quando inscrição é confirmada
-   **Nota:** Listeners devem ser criados nos módulos Treasury e Notifications

### 5. Controllers ✅

-   Admin:
    -   `EventController.php` - CRUD de eventos
    -   `RegistrationController.php` - Gerenciamento de inscrições
-   Public:
    -   `EventController.php` - Visualização pública de eventos
-   MemberPanel:
    -   `EventController.php` - Eventos para membros com pré-preenchimento

### 6. Routes ✅

-   Admin: `/admin/events/*`
-   Public: `/eventos/*`
-   MemberPanel: `/painel/eventos/*`

### 7. Seeder ✅

-   `EventsDatabaseSeeder.php` - Cria evento exemplo com 3 faixas de preço:
    -   Crianças (0-10 anos): Grátis (R$ 0,00)
    -   Adolescentes (11-17 anos): Meia (R$ 150,00)
    -   Adultos (18+ anos): Inteira (R$ 300,00)

## Funcionalidades Implementadas

### ✅ Precificação Dinâmica

-   Sistema calcula automaticamente o preço baseado na idade do participante
-   Suporta múltiplas regras de preço por evento
-   Validação de idade contra regras configuradas

### ✅ Validação de Capacidade

-   Verifica se evento tem vagas disponíveis antes de permitir inscrição
-   Suporta capacidade ilimitada (null)

### ✅ Flexibilidade de Público

-   `public`: Evento público para qualquer pessoa
-   `members`: Apenas para membros (MemberPanel)
-   `both`: Disponível em ambos, mas com facilidades para membros

### ✅ Inscrição Multi-Pessoa

-   Permite inscrever múltiplos participantes em uma única inscrição
-   Calcula valor total automaticamente
-   Gera um único registro financeiro

## Integrações Necessárias

### 1. Treasury Module

Criar listener em `Modules/Treasury/app/Listeners/RegistrationConfirmedListener.php`:

```php
use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Treasury\App\Services\TreasuryService;

public function handle(RegistrationConfirmed $event)
{
    $registration = $event->registration;

    // Criar entrada na tesouraria
    TreasuryService::createEntry([
        'type' => 'credit',
        'amount' => $registration->total_amount,
        'category' => 'Eventos',
        'description' => "Inscrição: {$registration->event->title}",
        'reference' => "REG-{$registration->id}",
    ]);
}
```

### 2. Notifications Module

Criar listener em `Modules/Notifications/app/Listeners/SendEventRegistrationNotification.php`:

```php
use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Notifications\App\Services\NotificationService;

public function handle(RegistrationConfirmed $event)
{
    $registration = $event->registration;

    // Enviar email com comprovante e QR Code
    NotificationService::sendEventRegistrationConfirmation($registration);
}
```

### 3. PaymentGateway Module

Integrar no fluxo de inscrição para processar pagamentos antes de confirmar.

## Status de Implementação

### ✅ Completo:

1. **Form Requests** ✅

    - `StoreEventRequest.php` - Validação para criar eventos
    - `UpdateEventRequest.php` - Validação para atualizar eventos
    - `RegisterEventRequest.php` - Validação para inscrições (com validação dinâmica de campos personalizados)

2. **Views Admin** ✅

    - `admin/events/index.blade.php` - Listagem de eventos
    - `admin/events/create.blade.php` - Criar evento
    - `admin/events/edit.blade.php` - Editar evento
    - `admin/events/show.blade.php` - Detalhes do evento
    - `admin/registrations/index.blade.php` - Listagem de inscrições
    - `admin/registrations/show.blade.php` - Detalhes da inscrição

3. **Views Public** ✅

    - `public/index.blade.php` - Listagem de eventos públicos
    - `public/show.blade.php` - Detalhes do evento público
    - `public/registration/pending.blade.php` - Inscrição pendente (público)
    - `public/registration/confirmed.blade.php` - Inscrição confirmada (público)

4. **Views MemberPanel** ✅

    - `memberpanel/index.blade.php` - Listagem de eventos para membros
    - `memberpanel/show.blade.php` - Detalhes do evento para membros
    - `memberpanel/my-registrations.blade.php` - Inscrições do membro
    - `memberpanel/registration-show.blade.php` - Detalhes da inscrição do membro
    - `memberpanel/registration/pending.blade.php` - Inscrição pendente (membro)
    - `memberpanel/registration/confirmed.blade.php` - Inscrição confirmada (membro)

5. **Sidebar Integration** ✅

    - Link adicionado no sidebar Admin
    - Link adicionado no sidebar MemberPanel

6. **Controllers Completos** ✅

    - Admin: `EventController`, `RegistrationController`
    - Public: `EventController` (com registro público funcional)
    - MemberPanel: `EventController` (com pré-preenchimento de dados e método `showRegistration`)

7. **Funcionalidades Admin Avançadas** ✅
    - Dashboard com estatísticas completas
    - Gráfico de arrecadação financeira (últimos 30 dias) usando Chart.js
    - Distribuição de participantes por faixa etária
    - Filtros por status e idade nas inscrições
    - Exportação de lista de presença em PDF
    - Exportação de lista de presença em Excel/CSV

### ✅ Integrações Completas:

1. **Listeners Criados e Registrados** ✅

    - ✅ `RegistrationConfirmedListener` no Treasury - Cria entrada financeira automaticamente
    - ✅ `SendEventRegistrationNotification` no Notifications - Envia email com comprovante
    - ✅ Template de email criado em `resources/views/emails/registration-confirmed.blade.php`

2. **Integração com PaymentGateway** ✅

    - ✅ Integração completa nos controllers Public e MemberPanel
    - ✅ Criação automática de pagamento quando inscrição tem valor > 0
    - ✅ Listener `ConfirmRegistrationOnPaymentCompleted` para confirmar inscrição quando pagamento é completado
    - ✅ Fluxo completo: Inscrição → Pagamento → Confirmação → Treasury → Notificação

### ⚠️ Pendente (Opcional):

1. **Testes** - Criar testes unitários e de integração

## Como Usar

1. Execute as migrations:

```bash
php artisan migrate
```

2. Execute o seeder:

```bash
php artisan db:seed --class=Modules\\Events\\Database\\Seeders\\EventsDatabaseSeeder
```

3. Acesse:

-   Admin: `/admin/events/events`
-   Público: `/eventos`
-   MemberPanel: `/painel/eventos`

## Estrutura de Dados

### Event

-   Título, slug, descrição
-   Datas de início/fim
-   Localização (string ou JSON)
-   Capacidade (null = ilimitado)
-   Status: draft, published, closed
-   Visibilidade: public, members, both
-   Formulário customizado (JSON)

### EventPriceRule

-   Evento relacionado
-   Label (ex: "Crianças", "Adultos")
-   Idade mínima/máxima
-   Preço
-   Ordem de exibição

### Registration

-   Evento relacionado
-   Usuário (nullable para visitantes)
-   Valor total
-   Status: pending, confirmed, cancelled, refunded
-   Método de pagamento
-   Referência de pagamento

### Participant

-   Inscrição relacionada
-   Dados pessoais (nome, email, data nascimento, documento, telefone)
-   Respostas customizadas (JSON)
-   Check-in status
