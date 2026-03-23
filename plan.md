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
