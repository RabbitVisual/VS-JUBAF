# ROADMAP - Vertex JUBAF

Roadmap oficial para evolucao funcional e tecnica da plataforma Vertex JUBAF, considerando o estado atual do sistema apos a refatoracao modular.

## Contexto atual

- Base Laravel modular estabilizada com `nwidart/laravel-modules`.
- Modulos ativos: `HomePage`, `Admin`, `MemberPanel`, `Notifications`, `Bible`, `PaymentGateway`, `Treasury`, `ChurchCouncil`, `Events`, `Sermons`, `LiderancaPanel`, `Igrejas`, `Comunicacao`.
- Escopo legado removido: bots, gamificacao e modulos nao aderentes ao objetivo JUBAF.
- Nomenclatura funcional consolidada para `lideranca` (rotas e painel).

## Objetivo macro 2026

Consolidar o Vertex JUBAF como plataforma de governanca, operacao e conexao da Juventude Batista Feirense, com foco em:

1. organizacao associativa entre igrejas e liderancas;
2. governanca e transparencia institucional;
3. operacao de eventos com fluxo financeiro rastreavel;
4. comunicacao oficial e engajamento continuo.

## Roadmap por fases

## Fase 1 - Consolidacao de Fundacao (concluida/parcial)

- [x] Limpeza de modulos nao aderentes ao escopo JUBAF.
- [x] Remocao de bots e recursos legacy.
- [x] Criacao dos modulos `Igrejas` e `Comunicacao`.
- [x] Ajuste do painel de lideranca (`LiderancaPanel`) e padrao de rotas `lideranca.*`.
- [x] Saneamento inicial de migracoes legadas que quebravam bootstrap.
- [x] Atualizacao da documentacao base (`README.md`).

## Fase 2 - Governanca e Operacao Core (prioridade alta)

### Igrejas
- [ ] CRUD completo de igrejas com validacoes de dominio.
- [ ] Cadastro de historico de lideranca por igreja.
- [ ] Vinculo de igrejas a eventos e comunicados segmentados.

### Comunicacao
- [ ] CRUD completo de postagens (`edital`, `ata`, `aviso`, `noticia`).
- [ ] Publicacao com anexos e trilha de auditoria.
- [ ] Controle de visibilidade (publico, lideranca, interno).

### LiderancaPanel + ChurchCouncil
- [ ] Fluxo completo de pautas, reunioes, aprovacoes e decisoes.
- [ ] Painel sintetico de indicadores de governanca.
- [ ] Exportacoes e historico institucional.

## Fase 3 - Ciclo operacional integrado (prioridade alta)

### Fluxo Eventos -> Pagamentos -> Tesouraria -> Notificacoes
- [ ] Garantir idempotencia ponta a ponta no fechamento financeiro de inscricoes.
- [ ] Dashboard de conciliacao entre inscricoes, pagamentos e lancamentos.
- [ ] Alertas operacionais automaticos para divergencias e pendencias.
- [ ] Templates de notificacao por evento e por etapa do ciclo.

### Tesouraria
- [ ] Evoluir relatorios para prestacao de contas por periodo e centro de custo.
- [ ] Melhorar trilha de auditoria de alteracoes financeiras.

## Fase 4 - Escala e Experiencia (prioridade media)

- [ ] Melhorias de UX mobile-first nos paineis Member e Lideranca.
- [ ] Paginas de onboarding para equipe de diretoria e secretariado.
- [ ] Bibliotecas de componentes e padrao visual institucional.
- [ ] Otimizacoes de performance e observabilidade.

## Backlog estrategico

- [ ] API publica controlada para integracoes externas futuras.
- [ ] Notificacoes multicanal (preparacao para conectores externos).
- [ ] Relatorios analiticos de participacao por igreja e por regiao.

## Criterios de pronto por fase

Cada fase deve ser considerada concluida somente com:

- validacao funcional pelos fluxos principais;
- validacao tecnica (`composer dump-autoload`, `php artisan route:list`, testes aplicaveis);
- documentacao atualizada (`README.md`, `ROADMAP.md`, `docs/ARCHITECTURE.md`);
- ausencia de referencias quebradas no bootstrap e nos paineis principais.

## Indicadores de sucesso

- estabilidade de deploy sem regressao de rotas/autoload;
- rastreabilidade do ciclo financeiro de eventos;
- reducao de operacao manual da diretoria;
- aumento de uso de comunicacao oficial via plataforma;
- consistencia de governanca entre lideranca e igrejas vinculadas.
