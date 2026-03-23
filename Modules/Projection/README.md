# Projection Module

Live sanctuary media: lyrics, Bible verses, and service slides in real time (JS-based polling, no WebSockets).

## API (v1 only)

All consumers use **`/api/v1/projection/*`**. No legacy `projection/api` routes.

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `state` | GET, POST | web + auth | Get/update projection state (type, content, theme, blackout, current setlist/slide) |
| `state/next-slide` | POST | web + auth | Advance to next slide or first slide of next item |
| `state/prev-slide` | POST | web + auth | Go to previous slide or last slide of previous item |
| `viewer/state` | GET | viewer_token query | Public state for screen when `PROJECTION_VIEWER_TOKEN` is set |
| `assets` | GET, POST | web + auth | List / upload media assets |
| `assets/{id}` | DELETE | web + auth | Delete asset |
| `assets/serve/{filename}` | GET | throttle only | Serve file (for screen loading images/videos) |
| `slides` | GET, POST | web + auth | Custom slides list / create (UI: Console > Library > Slides tab) |
| `lyrics/search` | GET | web + auth | Search lyrics (Worship) |
| `timeline/items` | POST | web + auth | Add item to setlist timeline |
| `timeline/items/{id}` | DELETE | web + auth | Remove timeline item |
| `timeline/reorder` | POST | web + auth | Reorder timeline (body: `{ items: [{ id, order }] }`) |
| `events/upcoming` | GET | web + auth | Upcoming events (for event_spotlight) |
| `themes` | GET, POST | web + auth | List / create themes |
| `themes/{id}` | GET, PUT, DELETE | web + auth | Show / update / delete theme |
| `themes/{id}/default` | POST | web + auth | Set default theme |
| `card-templates` | GET | web + auth | List card templates |
| `card-templates/{id}` | GET | web + auth | Show card template |

Responses are always `{ data }` on success.

## Timeline item types

Supported in `addTimelineItem` and in Worship setlist payload:

- `song` — from repertoire or pasted lyrics (ChordPro)
- `bible` — reference + slides
- `card` — custom text slide
- `video`, `audio`, `image` — media (asset_id or url)
- `countdown` — duration_seconds, label
- `section_header` — title
- `announcement` — text
- `event_spotlight` — event_id

Slides for each type are built by `WorshipApiService::getSetlistWithItems()`; Projection only reads state and drives next/prev.

## State shape

See `ProjectionApiService::defaultState()`: `type`, `content`, `footer`, `theme`, `isBlackout`, `isClear`, `bgUrl`, `bgType`, `bgOpacity`, `bgBlur`, `alertMessage`, `stage_alert`, `transition`, `logoOption`, `logoMessage`, `worshipFx`, `url`, `current_setlist_id`, `current_item_id`, `current_slide_index`, `current_item_title`, `animation_style`, `stage_content`, `countdown_seconds`, `countdown_ends_at` (Unix timestamp para sincronizar todas as telas), etc.

- **Countdown**: o servidor grava `countdown_ends_at` ao receber um countdown; todas as telas (admin, painel, projecao/tela) usam esse valor e ficam sincronizadas. Ao chegar em 0, o state passa a `type: 'logo'` com a mensagem configurada (ex.: "Sejam Bem Vindos!").

## Views

- **Admin** — `admin/projection` (index, console, screen, remote, themes, card-templates, team).
- **MemberPanel** — `painel/projection` (index, console, screen, remote).

Console is a Vue SPA; Remote and Screen are Blade + Alpine or vanilla JS with polling.

## Config / Viewer token

- **Admin:** **Projeção > Configurações** — ativar/desativar "Tela sem login" e definir o token do viewer. Prioridade sobre o .env.
- **.env (fallback):** `PROJECTION_VIEWER_TOKEN` — quando a opção está ativada no Admin e o token não foi definido na tela, o valor do .env é usado.
- Com viewer ativado e token definido, `GET /api/v1/projection/viewer/state?viewer_token=XXX` devolve o state sem autenticação.

### URL completa da tela (sem login)

1. Ative "Tela sem login" em **Admin > Projeção > Configurações**.
2. Defina um token secreto (ex.: `meu-token-123`).
3. No dispositivo do projetor, abra no navegador a **URL da tela** (não a URL da API):
   - **URL correta (página HTML):** `https://seusite.com/projecao/tela?viewer_token=meu-token-123`
   - **Não use** a URL da API (`/api/v1/projection/viewer/state?viewer_token=...`) no navegador — ela devolve só JSON.
4. A página `/projecao/tela` carrega o canvas e faz polling em `GET /api/v1/projection/viewer/state?viewer_token=...` para exibir o conteúdo.
