# Checklist de deploy (produção)

Use este checklist ao subir a aplicação para produção para garantir que tudo funcione 100% após o build.

## 1. Variáveis de ambiente

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` (obrigatório em produção: evita vazamento de stack traces e dados sensíveis)
- [ ] `APP_KEY` definida (gerar com `php artisan key:generate` se necessário)
- [ ] `APP_URL` com a URL final do site (ex.: `https://seusite.com.br`)
- [ ] Se os assets forem servidos de outro domínio/CDN: definir `ASSET_URL` (ex.: `https://cdn.seusite.com.br`)
- [ ] Se a aplicação estiver atrás de proxy reverso (nginx, Cloudflare, etc.): configurar `TrustProxies` em `bootstrap/app.php` conforme necessidade (ex.: `at: ['192.168.1.1']` em vez de `'*'` em ambientes controlados)

## 2. Composer

```bash
composer install --no-dev --optimize-autoloader
```

## 3. Frontend (Vite)

```bash
npm ci
npm run build
```

- Servir a pasta `public/` (incluindo `public/build`) pelo servidor web.
- **Não** rodar `npm run dev` em produção; usar apenas os arquivos gerados por `npm run build`.

### Se `npm ci` falhar com EPERM no Windows

O erro costuma ser arquivo em uso (ex.: `tailwindcss-oxide.win32-x64-msvc.node`). Faça:

1. **Parar qualquer processo que use Node**: feche o terminal onde está `npm run dev` (ou Vite) e qualquer outro que use o projeto.
2. **Em um novo terminal**, na pasta do projeto:
   - **Opção A** – Reinstalar do zero (remover `node_modules` e instalar de novo):
     ```powershell
     npm run clean:deps
     npm ci
     npm run build
     ```
     Se `npm run clean:deps` der EPERM, use no PowerShell: `Remove-Item -Recurse -Force node_modules -ErrorAction SilentlyContinue` e depois `npm ci` e `npm run build`.
   - **Opção B** – Só buildar sem reinstalar (se `node_modules` já estiver íntegro):
     ```bash
     npm run build
     ```
   - **Opção C** – Usar `npm install` em vez de `npm ci` (evita apagar e recriar `node_modules`):
     ```bash
     npm install
     npm run build
     ```
3. Se ainda der EPERM, execute o terminal **como Administrador** ou reinicie o PC e tente de novo (antivirus/IDE às vezes seguram o `.node`).

### Aviso "cleanup Failed to remove some directories" (Windows)

Se ao rodar `npm install` ou `npm ci` aparecer aviso de *cleanup* relacionado a `@tailwindcss`/`.oxide-win32-x64-msvc`: é inofensivo desde que o fim da saída mostre *"added X packages"* e que depois **`npm run build`** termine com sucesso. O binário do Tailwind fica em uso no Windows; feche `npm run dev` antes de instalar para reduzir esse aviso.

## 4. Cache e otimizações Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

- Garantir permissões corretas em `storage/` e `bootstrap/cache/` (o servidor web precisa escrever nesses diretórios quando necessário).

## 5. Banco e migrações

- [ ] Migrações executadas (`php artisan migrate --force` em produção)
- [ ] Tabela de settings existente (necessária para `SettingsHelper::applyGlobalSettings()` no boot)

## 5.1 Seeds (dados de demonstração)

Em produção **não** rodar seeds de dados de demonstração (EBD completa, Worship Academy demo, HomePage vitrine, etc.). Esses seeds são pensados para **ambiente local/homologação**.

- O seeder raiz `DatabaseSeeder` já é chamado automaticamente por `php artisan db:seed` e faz apenas:
  - Criação de um usuário de teste idempotente (`test@example.com`);
  - Seed de Gateways de Pagamento;
  - Seed do módulo Tesouraria;
  - Seed do módulo Events (evento exemplo).
- No ambiente **local** ele também chama `LocalDemoSeeder`, que popula dados de demonstração em vários módulos (Worship, EBD, HomePage, Gamification, Treasury, etc.).

Recomendações:

- **Produção**:
  - Evitar `php artisan db:seed` depois de o sistema estar com dados reais.
  - Se precisar rodar um seeder específico em produção (ex.: novo tipo de evento), rodar de forma **pontual e consciente**, por exemplo:
    - `php artisan db:seed --class="Modules\\Events\\Database\\Seeders\\EventTypesSeeder"`
- **Homologação/Desenvolvimento**:
  - Rodar apenas:
    ```bash
    php artisan db:seed
    ```
    Isso executa `DatabaseSeeder` + `LocalDemoSeeder` (quando `APP_ENV` é `local`/`development`), populando todos os módulos de teste sem apagar dados existentes.

## 6. Servidor web

- Document root deve apontar para a pasta `public/` do projeto.
- Não expor a raiz do projeto (`.env`, `vendor/`, etc.) diretamente na web.

## Resumo rápido (ordem sugerida)

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

Depois de subir, conferir no browser que não há 404 em CSS/JS e que os avisos de preload (se existiam em dev) estão resolvidos em produção.
