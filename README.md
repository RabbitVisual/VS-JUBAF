# Vertex JUBAF (VS-JUBAF)

Plataforma oficial da Juventude Batista Feirense para gestao associativa, comunicacao institucional, governanca e operacao ministerial.

> Projeto mantido por Vertex Solutions LTDA, evoluido a partir da base VertexCBAV com refatoracao modular para o contexto JUBAF.

## Header institucional

O Vertex JUBAF e um sistema web modular, desenvolvido para:

- conectar lideranca da juventude com igrejas e congregacoes vinculadas;
- organizar governanca (conselho, aprovacoes, reunioes, documentos);
- operar eventos, comunicacao e acompanhamento ministerial;
- dar transparencia financeira e registrar a vida associativa;
- centralizar dados biblicos e notificacoes transacionais.

## Navegacao da documentacao

- Visao funcional e planejamento: [`ROADMAP.md`](ROADMAP.md)
- Visao tecnica e onboarding de arquitetura: [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)

## Status atual do sistema (2026)

O sistema ja passou pela limpeza estrutural planejada e esta operando com foco no escopo JUBAF.

- Modulos removidos do projeto: `Gamification`, `Assets`, `EBD`, `Marketplace`, `SocialAction`, `Projection`, `Ministries`, `Worship`.
- Bots removidos: `CbavBot` e `EliasBot`.
- Padronizacao de nomenclatura concluida: `LiderancaPanel` e rotas `lideranca.*`.
- Fundacao de novos modulos criada: `Igrejas` e `Comunicacao`.

## Modulos ativos

De acordo com `modules_statuses.json`, os modulos ativos sao:

- `HomePage`
- `Admin`
- `MemberPanel`
- `Notifications`
- `Bible`
- `PaymentGateway`
- `Treasury`
- `Diretoria`
- `Events`
- `Sermons`
- `LiderancaPanel`
- `Igrejas`
- `Comunicacao`

## Arquitetura funcional

### 1) Camada publica

- Portal institucional e conteudo da homepage.
- Exibicao de informacoes publicas conforme regras de cada modulo.

### 2) Camada operacional autenticada

- `MemberPanel`: experiencia do membro.
- `LiderancaPanel`: operacao da lideranca da juventude.
- `Admin`: administracao central do sistema.

### 3) Camada de servicos transversais

- `Notifications`: notificacoes in-app e preferencias.
- `PaymentGateway`: pagamentos e webhooks canonicos.
- `Treasury`: controle financeiro, campanhas, metas e relatorios.
- `Bible`: base biblica local e servicos de leitura.

## Modulos e responsabilidades (estado atual)

| Modulo           | Responsabilidade principal                                                   |
| ---------------- | ---------------------------------------------------------------------------- |
| `Admin`          | Usuarios, papeis, configuracoes globais, operacao administrativa central.    |
| `HomePage`       | Conteudo institucional publico (cms da landing).                             |
| `MemberPanel`    | Painel do membro e fluxos de participacao.                                   |
| `LiderancaPanel` | Painel de lideranca para acompanhamento ministerial, conselho e operacao.    |
| `Diretoria`      | Reunioes, pautas, aprovacoes, historico de decisoes e governanca.            |
| `Events`         | Ciclo de eventos, inscricoes, lotes e check-in.                              |
| `Sermons`        | Acervo e gestao de sermoes, series, estudos e comentarios.                   |
| `Treasury`       | Lancamentos financeiros, campanhas, metas, relatorios e prestacao de contas. |
| `PaymentGateway` | Integracao de pagamentos (Stripe, Mercado Pago, PIX) e webhook unico.        |
| `Notifications`  | Centro de notificacoes internas e templates.                                 |
| `Bible`          | Referencia biblica local, planos e recursos de leitura.                      |
| `Igrejas`        | Cadastro e gestao de igrejas/congregacoes vinculadas.                        |
| `Comunicacao`    | Feed oficial da diretoria: editais, atas, avisos e noticias.                 |

## Principais decisoes de refatoracao ja aplicadas

- Limpeza de namespaces PSR-4 de modulos removidos.
- Remocao de rotas, menus e dependencias de modulos excluidos.
- Remocao de componentes e servicos de bot legados.
- Ajustes de rotas e middleware para padrao `lideranca`.
- Saneamento de migracoes legadas que referenciavam recursos removidos.

## Estrutura tecnica

- Framework: `Laravel 12` (PHP `8.2+`)
- Modularizacao: `nwidart/laravel-modules`
- Frontend: `Vite` + `Tailwind CSS`
- Banco: `MySQL`
- Autenticacao/ACL: abordagem por papeis e regras de acesso por painel
- Pagamentos: Stripe, Mercado Pago e PIX via modulo dedicado

## Rotas e paineis

- Publicas: `routes/web.php`
- Administrativas: `routes/admin.php`
- Membro: `routes/member.php`
- Lideranca: `routes/lideranca.php`
- API: `routes/api.php`

Padrao atual:

- sem prefixo legado `pastor.*`
- uso de prefixo e nomes `lideranca.*`

## Setup de desenvolvimento

### Requisitos

- PHP 8.2+
- Composer 2.x
- Node.js 22+
- MySQL 8+

### Instalacao

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
```

### Comandos uteis

```bash
# desenvolvimento completo (Windows)
composer run dev

# desenvolvimento completo com Pail (Linux/macOS com pcntl)
composer run dev:with-pail

# limpar caches
php artisan optimize:clear

# atualizar autoload
composer dump-autoload

# listar rotas
php artisan route:list
```

> Observacao: migrations e seeds devem seguir o alinhamento do ambiente/projeto vigente antes de execucao em homologacao/producao.
> Em Windows, o `laravel/pail` nao roda sem extensao `pcntl`; por isso use `composer run dev` (sem Pail).

## Credenciais de demo (ambiente local/dev)

Senha padrao para auto login e seeders dev: `password`

- Super Admin: `superadmin@jubaf.com.br`
- Lideranca: `lideranca@jubaf.com.br`
- Membro: `membro@jubaf.com.br`
- Admin fixo: `admin@jubaf.com.br`

### Como testar auto login (dev)

1. Inicie o ambiente local com `composer run dev`.
2. Acesse `/login`.
3. Na caixa "Ferramentas de Desenvolvedor", use um dos botoes: `SuperAdmin`, `Lideranca` ou `Membro`.

> Os botoes de auto login aparecem somente em ambiente `local/development/dev`.

## Roadmap funcional (JUBAF)

1. Consolidar governanca (`Diretoria` + `LiderancaPanel`) com fluxos completos.
2. Evoluir `Igrejas` para operacao multi-congregacao (vinculos, historico de lideranca, indicadores).
3. Evoluir `Comunicacao` para feed oficial com anexos, trilha de publicacao e distribuicao.
4. Integrar eventos + financeiro + notificacoes para ciclo completo de inscricao e transparencia.
5. Expandir painel de indicadores estrategicos para diretoria.

## Prompt mestre (prompt do prompt)

Use o bloco abaixo como prompt base para continuidade da evolucao do projeto em qualquer nova sessao de IA:

```text
Atue como Arquiteto de Software Senior no projeto Vertex JUBAF.

Contexto:
- O sistema e modular com nwidart/laravel-modules.
- O escopo atual e JUBAF (Juventude Batista Feirense), nao mais VertexCBAV generico.
- Modulos removidos: Gamification, Assets, EBD, Marketplace, SocialAction, Projection, Ministries, Worship.
- Modulos ativos: HomePage, Admin, MemberPanel, Notifications, Bible, PaymentGateway, Treasury, Diretoria, Events, Sermons, LiderancaPanel, Igrejas, Comunicacao.
- Nomenclatura oficial: Lideranca (nao usar pastor/pastoral em novos recursos).

Diretrizes:
1) Nao recriar recursos removidos.
2) Priorizar estabilidade de autoload, rotas e integracoes entre modulos ativos.
3) Implementar funcionalidades com foco em governanca, eventos, financeiro, comunicacao e base biblica local.
4) Manter padrao de codigo Laravel modular, com rotas claras, services e validacoes.
5) Sempre entregar impacto funcional + checklist de validacao tecnica.

Objetivo da tarefa:
[descrever aqui a feature/refatoracao desejada]
```

## Licenca

Uso privado/proprietario, conforme politicas da Vertex Solutions LTDA e diretrizes internas da JUBAF.
