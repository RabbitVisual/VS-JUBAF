# System Architecture & Development Standards

## 1. Local-First & Internal Resource Philosophy
The application is designed to be **entirely self-contained** and prioritized towards internal data.
*   **No External CDNs**: All assets (CSS, JS, Fonts, Icons) must be served from the local `public/` directory.
*   **No External API Dependencies**: Avoid external cloud services for core functionality.
*   **Internal Source of Truth**: Before using an external service (Google, Bible APIs, etc.), check if the resource exists locally.
    *   **Bible Data**: Use `Modules/Bible`. It contains complete offline versions.
    *   **Worship Resources**: Use `Modules/Worship` for chords and liturgy.
*   **Local Data Power**: We maintain rich local databases to ensure offline capability and maximum speed.

## 2. Real-Time & Live Features (Low-Cost)
*   **Avoid High-Cost Infrastructure**: Do not implement features that require continuous terminal processes (like Reverb/WebSockets) for now.
*   **JS-Centric Live**: Use **Alpine.js**, **Livewire Polling**, or simple **Vanilla JS** for real-time effects like count-downs, live status, or updates.
*   **Scalability**: Write your JS/Livewire logic cleanly so it can be swapped for WebSockets (Reverb) in the future if needed, but keep it "low-cost JS" by default.

## 3. Core Technology Stack
*   **Backend**: Laravel 12.x (PHP 8.2+)
*   **Frontend**: Alpine.js v4 (Reactive UI) & Blade Templates
*   **Styling**: **Tailwind CSS v4.1** (Stored and compiled locally via Vite)
*   **Architecture**: Modular (`nwidart/laravel-modules`)

## 4. Iconografia e Fontes (100% Locais)
O sistema opera em modo **Offline-Ready**, sem dependência de CDNs ou links externos para ativos visuais.

### Fontes (Self-Hosted)
Utilizamos as famílias **Inter** e **Poppins**, servidas localmente para garantir performance e privacidade.
*   **Sans-serif**: Inter (Corpo e UI Geral)
*   **Display**: Poppins (Títulos e Destaques)
*   **Implementação**: Definidas via `@font-face` no `resources/css/app.css` e gerenciadas pelo pacote `log1x/laravel-webfonts`.
*   **Uso em Templates**: Utilize a diretiva `@preloadFonts` no `<head>` de todos os layouts para carregamento otimizado.

### Ícones (Font Awesome 7.1 Pro)
Utilizamos o **Font Awesome 7.1 Pro Duotone** como linguagem visual primária.
*   **Política Zero CDN**: É proibido o uso de Kits FA ou links de CDNs externos.
*   **Componente Blade**: Forçamos o uso de `<x-icon name="icon-name" style="duotone" class="optional-classes" />`.
*   **Ativos**: Localizados em `public/vendor/fontawesome-pro/`.
*   **Vantagem**: Funciona 100% sem internet e preserva a privacidade dos usuários.

## 5. Styling & Layout (Tailwind CSS v4.1)
Tailwind CSS v4.1 is integrated locally.
*   **Zero CDN Policy**: Do not use the Tailwind Play CDN. All styles must be compiled through Vite into `app.css`.
*   **UI Patterns**: Use Flowbite (local version) for complex components.
*   **Error Pages**: Premium, professional, and comical (found in `resources/views/errors/`).

## 6. Global Input Masking & Logic
*   **iMask.js**: Local initialization in `resources/js/masks.js` for CPF, Phone, etc.
*   **Loading UX**: The `<x-loading-overlay />` is a local component handled via Alpine.js.

## 7. Module Ecosystem (Comprehensive Map)
Separation of concerns is maintained through these 17 specialized modules:

| Module             | Description                                                                                  |
| :----------------- | :------------------------------------------------------------------------------------------- |
| **Admin**          | Central command. Management of users, roles, global settings, and audit logs.                |
| **Assets**         | Inventory management. Tracking physical church property and digital resources.               |
| **Bible**          | **The Spiritual Core.** Complete offline Bible database. ALWAYS use this for verses/liturgy. |
| **ChurchCouncil**  | Governance Hub. Meeting minutes, leadership tracking, and official decisions.                |
| **EBD**            | Sunday School (VerteAcademy). Gamification, classes, student progress, and leaderboards.     |
| **Events**         | Logistics & Ticketing. Calendar, registrations, payments, and QR check-in flows.             |
| **HomePage**       | Public Presence. CMS for landing page, testimonials, and dynamic church info.                |
| **Intercessor**    | Prayer Network. Moderation of requests and real-time prayer commitment tracking.             |
| **MemberPanel**    | User Dashboard. Personal profile, engagement tracking, and member-only features.             |
| **Ministries**     | Departmental Org. Management of Music, Youth, Women, and other church sectors.               |
| **Notifications**  | Alert System. Centralizes system messages and member communications.                         |
| **PaymentGateway** | Unified Payments. Handles Mercado Pago, Stripe, and PIX integrations for donations.          |
| **Projection**     | Sanctuary Media. Live lyrics, bible verse projection, and service announcements.             |
| **Sermons**        | Media Archive. Repository for video and audio preached messages.                             |
| **SocialAction**   | Charity & Outreach. Smart Pantry (Kit assembly) and beneficiary management.                  |
| **Treasury**       | Church Finances. Bookkeeping, budgeting, and financial reporting.                            |
| **Worship**        | Liturgy & Music. Repertoire, setlists, academy chords, and band organization.                |

## 8. Development Workflow
*   **Vite**: Keep `npm run dev` running. It handles local Tailwind 4.1 compilation.
*   **Check Local First**: Always verify if a feature can be served by `Modules/Bible` or `Modules/Worship` before looking outward.

## 9. Module Ecosystem (nwidart/laravel-modules)
The application is divided into specialized modules to ensure separation of concerns:

### Core & Administration
*   **Admin**: Centralized management dashboard and system settings.
*   **MemberPanel**: Dedicated portal for individual church members.
*   **Assets**: Management of physical and digital system assets.
*   **Notifications**: Internal alert system and push notifications via Laravel Reverb.

### Spiritual & Educational
*   **Bible**: Integration of sacred texts and search capabilities.
*   **EBD**: Management of Sunday Biblical School classes, students, and curriculum.
*   **Ministries**: Organization of internal departments (Youth, Women, etc.).
*   **Sermons**: Archive and management of preached messages and media.
*   **Intercessor**: Prayer request management and intercession chains.

### Operational & Social
*   **Events**: Calendar, registration, and logistics for church events.
*   **Worship**: Planning of services, liturgies, and musical repertoires.
*   **Projection**: Management of media and slides for live services.
*   **SocialAction**: Charitable activities and community support programs.
*   **HomePage**: Public-facing website content management.

### Financial & Governance
*   **Treasury**: Full financial control, accounting, and reporting.
*   **PaymentGateway**: Integration with Stripe and MercadoPago for tithes and offerings.
*   **ChurchCouncil**: Administrative records, meeting minutes, and leadership decisions.