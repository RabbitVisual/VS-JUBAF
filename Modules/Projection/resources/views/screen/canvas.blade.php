@php
    if (!isset($projectionStateUrl)) {
        $projectionStateUrl = url('/api/v1/projection/state');
    }
    if (!isset($projectionStateCredentials)) {
        $projectionStateCredentials = 'same-origin';
    }
    if (!isset($projectionCanPostState)) {
        $projectionCanPostState = true;
    }
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tela de Projeção</title>
    <!-- FontAwesome (Local) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-pro/css/all.css') }}">

    <style>
        /* CSS Reset & Alinhamento (CRÍTICO) */
        body {
            margin: 0; padding: 0; width: 100vw; height: 100vh;
            overflow: hidden; background-color: #000;
            font-family: 'Instrument Sans', 'Outfit', sans-serif;
        }

        #screen-container {
            width: 100vw; height: 100vh;
            display: flex; justify-content: center; align-items: center;
        }

        #bg-layer {
            position: absolute; inset: 0; z-index: 0;
            transition: all 1s ease-in-out;
            background-color: inherit; /* CRITICAL: Inherit from body to allow filters on solid colors */
        }

        #bg-layer img, #bg-layer video {
            width: 100%; height: 100%; object-fit: cover;
        }

        .content-box {
            position: relative; z-index: 10;
            text-align: center; max-width: 85vw;
            color: white;
            line-height: 1.2;
            text-shadow: 0 4px 10px rgba(0,0,0,0.5);
            transition: opacity 0.4s ease-in-out, transform 0.3s ease-out;
        }
        .content-box.animate-line .lyric-line { opacity: 0; transform: translateY(12px); }
        .content-box.animate-line .lyric-line.visible { opacity: 1; transform: translateY(0); transition: opacity 0.35s ease-out, transform 0.3s ease-out; }
        .content-box.animate-stanza .lyric-stanza { opacity: 0; transform: translateY(8px); }
        .content-box.animate-stanza .lyric-stanza.visible { opacity: 1; transform: translateY(0); transition: opacity 0.4s ease-out, transform 0.35s ease-out; }
        .content-box.content-fade-out { opacity: 0; }
        .content-box.content-fade-in { opacity: 1; }

        /* Card (título + subtítulo + descrição em linhas separadas) */
        .content-box .projection-card { text-align: center; max-width: 85vw; display: block; }
        .content-box .projection-card-title { display: block; font-weight: 900; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 1rem 0; line-height: 1.2; }
        .content-box .projection-card-title--xs { font-size: clamp(0.9rem, 2vw, 1.25rem); }
        .content-box .projection-card-title--sm { font-size: clamp(1.1rem, 2.8vw, 1.75rem); }
        .content-box .projection-card-title--md { font-size: clamp(1.5rem, 4vw, 3rem); }
        .content-box .projection-card-title--lg { font-size: clamp(1.75rem, 4.5vw, 3.5rem); }
        .content-box .projection-card-title--xl { font-size: clamp(2rem, 5.5vw, 4rem); }
        .content-box .projection-card-subtitle { display: block; font-weight: 700; margin: 0 0 1.25rem 0; opacity: 0.95; }
        .content-box .projection-card-subtitle--xs { font-size: clamp(0.75rem, 1.8vw, 1rem); }
        .content-box .projection-card-subtitle--sm { font-size: clamp(0.9rem, 2.2vw, 1.25rem); }
        .content-box .projection-card-subtitle--md { font-size: clamp(1rem, 2.5vw, 1.75rem); }
        .content-box .projection-card-subtitle--lg { font-size: clamp(1.2rem, 3vw, 2rem); }
        .content-box .projection-card-subtitle--xl { font-size: clamp(1.4rem, 3.5vw, 2.25rem); }
        .content-box .projection-card-description { display: block; font-weight: 500; line-height: 1.5; margin: 0; white-space: normal; }
        .content-box .projection-card-description--xs { font-size: clamp(0.7rem, 1.6vw, 0.95rem); }
        .content-box .projection-card-description--sm { font-size: clamp(0.85rem, 2vw, 1.2rem); }
        .content-box .projection-card-description--md { font-size: clamp(0.95rem, 2.2vw, 1.5rem); }
        .content-box .projection-card-description--lg { font-size: clamp(1.1rem, 2.6vw, 1.75rem); }
        .content-box .projection-card-description--xl { font-size: clamp(1.3rem, 3.2vw, 2rem); }
        .content-box .projection-card-description br { display: block; content: ''; margin-top: 0.5em; }

        /* Themes */
        body.theme-black { background-color: #000 !important; color: #fff !important; }
        body.theme-dark { background-color: #111 !important; color: #eee !important; }
        body.theme-modern-dark { background: #020617 !important; color: #f8fafc !important; }
        body.theme-paper { background-color: #f5f5dc !important; color: #1e293b !important; }
        body.theme-ethereal { background: linear-gradient(to bottom right, #312e81, #581c87) !important; color: #fff !important; }
        body.theme-vibrant-worship { background: linear-gradient(to bottom right, #064e3b, #134e4a) !important; color: #fff !important; }
        body.theme-classic-bible { background-color: #2a2318 !important; color: #f5e6d3 !important; }
        body.theme-solid-blue { background-color: #1e3a8a !important; color: #fff !important; }
        body.theme-solid-red { background-color: #7f1d1d !important; color: #fff !important; }
        body.theme-solid-purple { background-color: #4c1d95 !important; color: #fff !important; }

        .blackout { background-color: #000 !important; z-index: 100; position: fixed; inset: 0; }
        .hidden { display: none !important; }

        /* Alert Styling */
        #alert-box {
            position: fixed; top: 3rem; left: 50%; transform: translateX(-50%);
            z-index: 60; background: rgba(220, 38, 38, 0.9); color: white;
            padding: 1.5rem 3rem; border-radius: 2rem; border: 4px solid rgba(255,255,255,0.2);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5); backdrop-filter: blur(12px);
            display: flex; items-center: center; gap: 1.5rem;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0; visibility: hidden; margin-top: -50px;
        }

        #alert-box.active { opacity: 1; visibility: visible; margin-top: 0; }

        /* Bible Version Tag */
        .bible-version-tag {
            font-size: 1.25rem;
            font-weight: 900;
            text-transform: uppercase;
            color: #fbbf24;
            letter-spacing: 0.4em;
            margin-bottom: 2rem;
            opacity: 0.8;
            border-bottom: 2px solid rgba(251, 191, 36, 0.3);
            display: inline-block;
            padding-bottom: 0.5rem;
        }

        /* Ambient FX (Worship Particles) */
        #ambient-fx {
            position: absolute; inset: 0; z-index: 1;
            pointer-events: none; opacity: 0;
            transition: opacity 2s ease;
        }

        #ambient-fx.active { opacity: 1; }

        .particle {
            position: absolute;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(5px);
            animation: float-particle var(--duration) infinite ease-in-out;
        }

        @keyframes float-particle {
            0%, 100% { transform: translateY(0) translateX(0) scale(1); }
            50% { transform: translateY(-100px) translateX(50px) scale(1.2); }
        }

        .particle-icon {
            position: absolute;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            filter: blur(1px) drop-shadow(0 0 10px currentColor);
            animation: float-particle var(--duration) infinite ease-in-out;
            opacity: 0.6;
        }

        @keyframes float-particle {
            0%, 100% { transform: translateY(0) translateX(0) rotate(0deg) scale(1); }
            33% { transform: translateY(-50px) translateX(30px) rotate(10deg) scale(1.1); }
            66% { transform: translateY(20px) translateX(-40px) rotate(-10deg) scale(0.9); }
        }

        /* Watermark */
        #watermark {
            position: fixed;
            bottom: 1rem;
            right: 1.5rem;
            z-index: 1000;
            color: rgba(255, 255, 255, 0.08); /* Quase invisível */
            font-size: 0.75rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            pointer-events: none;
            user-select: none;
        }
    </style>
</head>
<body class="theme-black">
    <div id="bg-layer"></div>
    <div id="ambient-fx"></div>
    <div id="blackout-layer" class="blackout hidden"></div>

    <div id="screen-container">
        <div id="content-slot" class="content-box"></div>
    </div>

    <!-- Alert Box -->
    <div id="alert-box">
        <i class="fa-duotone fa-triangle-exclamation" style="font-size: 40px;"></i>
        <span id="alert-text" style="font-size: 2.5rem; font-weight: 900; text-transform: uppercase;"></span>
    </div>

    <script>
        var STATE_URL = @json($projectionStateUrl);
        var STATE_CREDENTIALS = @json($projectionStateCredentials);
        var CAN_POST_STATE = @json($projectionCanPostState);

        const container = document.body;
        const bgLayer = document.getElementById('bg-layer');
        const blackoutLayer = document.getElementById('blackout-layer');
        const slot = document.getElementById('content-slot');
        const alertBox = document.getElementById('alert-box');
        const alertText = document.getElementById('alert-text');

        const urlParams = new URLSearchParams(window.location.search);
        const isStageView = urlParams.get('view') === 'stage';

        let lastState = {};
        let alertTimeout = null;
        let logoVerse = null;
        let countdownInterval = null;
        let countdownEndTime = null;

        function escapeHtml(str) {
            if (str == null || typeof str !== 'string') return '';
            var div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function calculateVerseSize(text) {
            const len = (text || '').length;
            if (len < 50) return 4.5;
            if (len < 100) return 3.8;
            if (len < 200) return 3.2;
            if (len < 300) return 2.6;
            return 2.2;
        }

        function calculateFontSize(text) {
            const len = (text || '').replace(/<[^>]*>/g, '').length;
            if (len < 30) return 7;
            if (len < 60) return 6;
            if (len < 120) return 5;
            if (len < 200) return 4;
            return 3;
        }

        async function fetchRandomVerse(state) {
            try {
                var res = await fetch('/api/v1/bible/random');
                var text = await res.text();
                var json = {};
                try { if (text && typeof text === 'string') json = JSON.parse(text); } catch (e) {}
                var data = (res.ok && json && json.data) ? json.data : null;
                logoVerse = (data && typeof data.text === 'string') ? data : null;
                renderLogoMode(state || {});
            } catch (e) { logoVerse = null; renderLogoMode(state || {}); }
        }

        function renderLogoMode(state) {
            try {
                state = state || {};
                var messageHtml = '';
                var footerHtml = '';
                var option = state.logoOption || 'verse';

                if (option === 'verse' && logoVerse && typeof logoVerse.text === 'string') {
                    var size = calculateVerseSize(logoVerse.text);
                    var bookName = (logoVerse.book && logoVerse.book.name) ? String(logoVerse.book.name) : ((logoVerse.chapter && logoVerse.chapter.book && logoVerse.chapter.book.name) ? String(logoVerse.chapter.book.name) : '');
                    var chapterNum = (logoVerse.chapter && logoVerse.chapter.chapter_number != null) ? logoVerse.chapter.chapter_number : '';
                    var verseNum = (logoVerse.verse_number != null) ? logoVerse.verse_number : '';
                    var refText = [bookName, (chapterNum !== '' && verseNum !== '') ? (chapterNum + ':' + verseNum) : (chapterNum || verseNum)].filter(Boolean).join(' ');
                    messageHtml = '<p style="font-weight: 900; font-style: italic; color: rgba(255,255,255,0.9); font-size: ' + size + 'vw; line-height: 1.1;">' + escapeHtml(logoVerse.text) + '</p>';
                    if (refText) {
                        footerHtml = '<div style="display: flex; align-items: center; gap: 1rem; justify-content: center; margin-top: 1.5rem;"><div style="height: 1px; width: 3rem; background: linear-gradient(to right, transparent, rgba(245,158,11,0.5));"></div><span style="font-size: 1.8vw; font-weight: 900; text-transform: uppercase; color: #f59e0b; letter-spacing: 0.5em;">' + escapeHtml(refText) + '</span><div style="height: 1px; width: 3rem; background: linear-gradient(to left, transparent, rgba(245,158,11,0.5));"></div></div>';
                    }
                } else if (option === 'message' && state.logoMessage) {
                    var msg = String(state.logoMessage);
                    var sizeMsg = calculateVerseSize(msg);
                    messageHtml = '<p style="font-weight: 900; color: rgba(255,255,255,0.9); font-size: ' + sizeMsg + 'vw; line-height: 1.1; text-transform: uppercase; letter-spacing: 0.1em;">' + escapeHtml(msg) + '</p>';
                }

                slot.innerHTML = '<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%;"><img src="/storage/image/logo_icon.png" style="width: 15vw; max-width: 300px; margin-bottom: 2rem; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.5));"><div style="width: 85vw; position: relative;">' + (option === 'verse' ? '<i class="fa-solid fa-quote-left" style="color: rgba(245,158,11,0.2); font-size: 5vw; position: absolute; left: -4vw; top: -2vw;"></i>' : '') + messageHtml + footerHtml + '</div></div>';
            } catch (e) {}
        }

        let pollBackoffUntil = 0;

        function updateScreen() {
            if (Date.now() < pollBackoffUntil) return;
            fetch(STATE_URL, { credentials: STATE_CREDENTIALS })
                .then(function(r) {
                    if (r.status === 429) {
                        pollBackoffUntil = Date.now() + 15000;
                        return null;
                    }
                    if (!r.ok) {
                        if (r.status === 401 || r.status >= 500) pollBackoffUntil = Date.now() + 5000;
                        return null;
                    }
                    var ct = (r.headers && r.headers.get('content-type')) || '';
                    if (ct.indexOf('application/json') === -1) return null;
                    return r.text();
                })
                .then(function(text) {
                    if (text == null || typeof text !== 'string') return;
                    if (text.trim().indexOf('<') === 0) return;
                    var res;
                    try { res = JSON.parse(text); } catch (e) { return; }
                    var state = (res && res.data) ? res.data : (res || {});
                    if (!state || typeof state !== 'object') return;
                    try {
                // Blackout
                state.isBlackout ? blackoutLayer.classList.remove('hidden') : blackoutLayer.classList.add('hidden');

                // Alert logic — mostrar por 8s e limpar no servidor para não reaparecer ao atualizar
                var alertMsg = (state.alertMessage || state.stage_alert || '').trim();
                if (alertMsg && alertMsg !== (lastState.alertMessage || lastState.stage_alert || '').trim()) {
                    alertText.innerText = alertMsg;
                    alertBox.classList.add('active');
                    if (alertTimeout) clearTimeout(alertTimeout);
                    alertTimeout = setTimeout(function() {
                        alertBox.classList.remove('active');
                        if (CAN_POST_STATE) {
                            var tok = document.querySelector('meta[name="csrf-token"]');
                            if (tok) {
                                fetch(STATE_URL, { method: 'POST', credentials: STATE_CREDENTIALS, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': tok.content, 'Accept': 'application/json' }, body: JSON.stringify({ alertMessage: '', stage_alert: '' }) }).catch(function() {});
                            }
                        }
                    }, 8000);
                } else if (!alertMsg) {
                    alertBox.classList.remove('active');
                }

                // Theme & Background: prefer theme_config from API (custom themes), fallback to hardcoded theme-* class
                function applyTheme(state) {
                    if (!container) return;
                    var cfg = state.theme_config;
                    var themeClass = 'theme-' + (state.theme || 'black');
                    var classes = Array.prototype.slice.call(container.classList).filter(function(c) { return c.indexOf('theme-') !== 0; });
                    var bgType = (cfg && cfg.background_type) ? String(cfg.background_type).toLowerCase() : '';
                    var bgVal = (cfg && cfg.background_value != null) ? String(cfg.background_value).trim() : '';
                    if (cfg && typeof cfg === 'object' && (bgType || bgVal || cfg.text_color)) {
                        classes = classes.join(' ').trim();
                        container.className = classes;
                        container.style.backgroundImage = '';
                        container.style.backgroundSize = '';
                        container.style.backgroundPosition = '';
                        if (bgType === 'solid' && bgVal) {
                            container.style.background = bgVal;
                        } else if (bgType === 'gradient' && bgVal) {
                            container.style.background = bgVal;
                        } else if (bgType === 'image' && bgVal) {
                            var safeUrl = bgVal.replace(/\\/g, '').replace(/"/g, '%22').replace(/'/g, '%27');
                            container.style.background = 'transparent';
                            container.style.backgroundImage = 'url("' + safeUrl + '")';
                            container.style.backgroundSize = 'cover';
                            container.style.backgroundPosition = 'center';
                        } else if (bgVal) {
                            container.style.background = bgVal;
                        } else {
                            container.style.background = cfg.background_value || '#000000';
                        }
                        container.style.color = cfg.text_color || '#fff';
                        if (cfg.font_family) container.style.fontFamily = cfg.font_family;
                        else container.style.fontFamily = '';
                        if (cfg.font_size_base) container.style.fontSize = cfg.font_size_base;
                        else container.style.fontSize = '';
                        container.style.textShadow = cfg.text_shadow || '';
                        if (cfg.alignment) container.style.textAlign = cfg.alignment;
                        if (cfg.padding) container.style.padding = cfg.padding;
                    } else {
                        container.style.background = '';
                        container.style.backgroundImage = '';
                        container.style.backgroundSize = '';
                        container.style.backgroundPosition = '';
                        container.style.color = '';
                        container.style.fontFamily = '';
                        container.style.fontSize = '';
                        container.style.textShadow = '';
                        container.style.textAlign = '';
                        container.style.padding = '';
                        container.className = (classes.length ? classes.join(' ') + ' ' : '') + themeClass;
                    }
                }
                if (state.theme !== lastState.theme || state.theme_id !== lastState.theme_id || (state.theme_config && JSON.stringify(state.theme_config) !== JSON.stringify(lastState.theme_config))) {
                    applyTheme(state);
                }

                // Worship FX Toggle
                if (state.worshipFx) {
                    initAmbientFx();
                } else {
                    stopAmbientFx();
                }

                // Apply Filters (Blur & Dim)
                const blurVal = state.bgBlur !== undefined ? state.bgBlur : 0;
                const dimVal = state.bgOpacity !== undefined ? state.bgOpacity : 1;

                // We apply filter to bgLayer and ambientFx
                bgLayer.style.filter = `blur(${blurVal}px) brightness(${dimVal})`;

                const ambientFx = document.getElementById('ambient-fx');
                if (ambientFx) {
                    ambientFx.style.filter = `blur(${blurVal}px)`;
                    ambientFx.style.opacity = state.worshipFx ? dimVal : 0;
                    ambientFx.style.transition = 'opacity 0.5s ease-in-out';
                }

                if (state.bgUrl !== lastState.bgUrl) {
                    bgLayer.innerHTML = state.bgUrl ?
                        (state.bgType === 'video' ?
                            `<video src="${state.bgUrl}" autoplay loop muted></video>` :
                            `<img src="${state.bgUrl}">`)
                        : '';
                }

                // Content — Stage view shows only stage_content / stage_alert
                if (isStageView) {
                    const stageContent = state.stage_content || '';
                    const stageAlert = state.stage_alert || state.alertMessage || '';
                    slot.innerHTML = stageContent ? '<div style="font-size: 2.5vw; font-weight: 700;">' + escapeHtml(stageContent) + '</div>' : '';
                    if (stageAlert && stageAlert !== (lastState.stage_alert || lastState.alertMessage || '')) {
                        alertText.innerText = stageAlert;
                        alertBox.classList.add('active');
                        if (alertTimeout) clearTimeout(alertTimeout);
                        alertTimeout = setTimeout(function() {
                            alertBox.classList.remove('active');
                            if (CAN_POST_STATE) {
                                var tok = document.querySelector('meta[name="csrf-token"]');
                                if (tok) {
                                    fetch(STATE_URL, { method: 'POST', credentials: STATE_CREDENTIALS, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': tok.content, 'Accept': 'application/json' }, body: JSON.stringify({ alertMessage: '', stage_alert: '' }) }).catch(function() {});
                                }
                            }
                        }, 8000);
                    }
                    lastState = state;
                    return;
                }

                if (state.isClear) {
                    slot.innerHTML = '';
                    logoVerse = null;
                } else if (state.type === 'logo') {
                    if (lastState.type !== 'logo') {
                        slot.innerHTML = '<div class="animate-pulse">CARREGANDO...</div>';
                        fetchRandomVerse(state);
                    } else if (state.logoOption !== lastState.logoOption || state.logoMessage !== lastState.logoMessage) {
                        if (state.logoOption === 'verse' && !logoVerse) {
                             slot.innerHTML = '<div class="animate-pulse">CARREGANDO...</div>';
                             fetchRandomVerse(state);
                        } else {
                             renderLogoMode(state);
                        }
                    }
                } else if (state.type !== 'logo') {
                    var contentKey = (state.type || '') + '|' + (state.url || '') + '|' + (state.current_slide_index ?? '') + '|' + (state.current_item_id ?? '') + '|' + (state.content || '').length + '|' + (state.animation_style || 'none');
                    if (state.type === 'card') contentKey += '|' + (state.card_title || '') + '|' + (state.card_subtitle || '') + '|' + (state.card_description || '').length;
                    if (state.type === 'countdown') contentKey += '|' + (state.countdown_ends_at || '');
                    var lastContentKey = (lastState.type || '') + '|' + (lastState.url || '') + '|' + (lastState.current_slide_index ?? '') + '|' + (lastState.current_item_id ?? '') + '|' + (lastState.content || '').length + '|' + (lastState.animation_style || 'none');
                    if (lastState.type === 'card') lastContentKey += '|' + (lastState.card_title || '') + '|' + (lastState.card_subtitle || '') + '|' + (lastState.card_description || '').length;
                    if (lastState.type === 'countdown') lastContentKey += '|' + (lastState.countdown_ends_at || '');
                    if (contentKey !== lastContentKey) {
                    logoVerse = null;
                    if (countdownInterval) { clearInterval(countdownInterval); countdownInterval = null; }
                    const animStyle = (state.animation_style || 'none').toLowerCase();
                    const doTransition = (html, opts) => {
                        slot.classList.add('content-fade-out');
                        setTimeout(function() {
                            slot.innerHTML = '';
                            slot.classList.remove('content-fade-out');
                            slot.innerHTML = html;
                            slot.classList.add('content-fade-in');
                            slot.classList.remove('animate-line', 'animate-stanza');
                            if (opts && opts.animClass) {
                                slot.classList.add(opts.animClass);
                                const els = slot.querySelectorAll(opts.selector);
                                els.forEach((el, i) => setTimeout(function() { el.classList.add('visible'); }, 50 + i * (opts.delayMs || 80)));
                            }
                        }, 300);
                    };
                    if (state.type === 'card') {
                        var cardTitle = (state.card_title || '').toString().trim();
                        var cardSub = (state.card_subtitle || '').toString().trim();
                        var cardDesc = (state.card_description || '').toString().trim();
                        var ts = ['xs','sm','md','lg','xl'].indexOf((state.card_title_size || 'md').toString()) >= 0 ? state.card_title_size : 'md';
                        var ss = ['xs','sm','md','lg','xl'].indexOf((state.card_subtitle_size || 'md').toString()) >= 0 ? state.card_subtitle_size : 'md';
                        var ds = ['xs','sm','md','lg','xl'].indexOf((state.card_description_size || 'md').toString()) >= 0 ? state.card_description_size : 'md';
                        var cardHtml = '<div class="projection-card">';
                        if (cardTitle) cardHtml += '<span class="projection-card-title projection-card-title--' + ts + '">' + escapeHtml(cardTitle) + '</span>';
                        if (cardSub) cardHtml += '<span class="projection-card-subtitle projection-card-subtitle--' + ss + '">' + escapeHtml(cardSub) + '</span>';
                        if (cardDesc) cardHtml += '<span class="projection-card-description projection-card-description--' + ds + '">' + escapeHtml(cardDesc).replace(/\n/g, '<br>') + '</span>';
                        cardHtml += '</div>';
                        doTransition(cardHtml);
                    } else if (state.type === 'slide') {
                        const size = calculateFontSize(state.content);
                        let inner = state.content;
                        let animClass = null;
                        let selector = null;
                        if (animStyle === 'line') {
                            const parts = inner.split(/<br\s*\/?>/gi);
                            inner = parts.map(p => `<span class="lyric-line">${p}</span>`).join('');
                            animClass = 'animate-line';
                            selector = '.lyric-line';
                        } else if (animStyle === 'stanza') {
                            const parts = inner.split(/(?:<\/p>\s*<p>|\n\n+)/gi).filter(Boolean);
                            inner = parts.map(p => `<div class="lyric-stanza">${p}</div>`).join('');
                            animClass = 'animate-stanza';
                            selector = '.lyric-stanza';
                        }
                        const html = `<div style="font-size: ${size}vw; font-weight: 900;">${inner}</div>` + (state.footer ? '<div style="font-size: 1.5vw; color: #fbbf24; margin-top: 2rem; text-transform: uppercase; font-weight: 900; letter-spacing: 0.3em;">' + escapeHtml(state.footer) + '</div>' : '');
                        doTransition(html, animClass ? { animClass, selector } : null);
                    } else if (state.type === 'image' || state.type === 'video') {
                        var mediaUrl = (state.url && typeof state.url === 'string') ? state.url : '';
                        var html = mediaUrl ? (state.type === 'image' ? '<img src="' + mediaUrl.replace(/"/g, '&quot;') + '" style="max-width:100vw; max-height:100vh; object-fit:contain;">' : '<video src="' + mediaUrl.replace(/"/g, '&quot;') + '" autoplay loop muted style="max-width:100vw; max-height:100vh; object-fit:contain;"></video>') : '<div style="font-size: 2vw; color: #666;">Mídia indisponível</div>';
                        doTransition(html);
                    } else if (state.type === 'countdown' && (state.countdown_seconds > 0 || state.countdown_ends_at)) {
                        // countdown_ends_at (Unix s) sincroniza todas as telas; fallback: countdown_seconds a partir de agora
                        var endAtMs = state.countdown_ends_at ? (state.countdown_ends_at * 1000) : (Date.now() + (parseInt(state.countdown_seconds, 10) || 0) * 1000);
                        countdownEndTime = endAtMs;
                        function pad(n) { return n < 10 ? '0' + n : n; }
                        function tick() {
                            const left = Math.max(0, Math.ceil((countdownEndTime - Date.now()) / 1000));
                            const m = Math.floor(left / 60);
                            const s = left % 60;
                            const el = document.getElementById('countdown-display');
                            if (el) el.innerHTML = `<span style="font-size: 12vw; font-weight: 900; font-variant-numeric: tabular-nums;">${pad(m)}:${pad(s)}</span>`;
                            if (left <= 0 && countdownInterval) {
                                clearInterval(countdownInterval);
                                countdownInterval = null;
                                // Ao chegar em 0: mostrar logo com mensagem de boas-vindas (igual ao console)
                                var welcomeState = { logoOption: 'message', logoMessage: (state.logoMessage && state.logoMessage.trim()) ? state.logoMessage : 'Sejam Bem Vindos!' };
                                renderLogoMode(welcomeState);
                            }
                        }
                        const html = '<div id="countdown-display" style="display:flex;flex-direction:column;align-items:center;gap:1rem;"></div>' + (state.footer ? '<div style="font-size: 1.5vw; color: #fbbf24; margin-top: 2rem; text-transform: uppercase; font-weight: 900;">' + escapeHtml(state.footer) + '</div>' : '');
                        doTransition(html);
                        tick();
                        countdownInterval = setInterval(tick, 1000);
                    }
                    }
                }

                lastState = state;
                    } catch (err) { if (typeof console !== 'undefined' && console.error) console.error(err); }
            }).catch(function(e) {});
        }

        function initAmbientFx() {
            const fx = document.getElementById('ambient-fx');
            if (fx.classList.contains('active')) return;

            fx.innerHTML = '';
            const iconPool = ['fa-music', 'fa-itunes-note', 'fa-chord-diagram', 'fa-microphone-lines', 'fa-waveform', 'fa-volume-high', 'fa-guitar', 'fa-drum'];
            const colorPool = ['#6366f1', '#ec4899', '#a855f7', '#22c55e', '#f59e0b', '#06b6d4'];

            for (let i = 0; i < 30; i++) {
                const item = document.createElement('div');
                const isIcon = Math.random() > 0.4;

                if (isIcon) {
                    const icon = document.createElement('i');
                    const iconName = iconPool[Math.floor(Math.random() * iconPool.length)];
                    icon.className = `fa-duotone ${iconName}`;
                    item.appendChild(icon);
                    item.className = 'particle-icon';
                    item.style.color = colorPool[Math.floor(Math.random() * colorPool.length)];
                } else {
                    item.className = 'particle';
                }

                const size = Math.random() * (isIcon ? 80 : 300) + 40;
                const left = Math.random() * 100;
                const top = Math.random() * 100;
                const duration = Math.random() * 15 + 15;
                const delay = Math.random() * 10;

                item.style.width = `${size}px`;
                item.style.height = `${size}px`;
                if (isIcon) item.style.fontSize = `${size/1.5}px`;
                item.style.left = `${left}%`;
                item.style.top = `${top}%`;
                item.style.setProperty('--duration', `${duration}s`);
                item.style.animationDelay = `${delay}s`;

                fx.appendChild(item);
            }
            fx.classList.add('active');
        }

        function stopAmbientFx() {
            document.getElementById('ambient-fx').classList.remove('active');
        }

        var POLL_INTERVAL_MS = 1000;
        setInterval(updateScreen, POLL_INTERVAL_MS);
        updateScreen();

        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') updateScreen();
        });
    </script>
    <div id="watermark">© 2026 Vertex Solutions LTDA. Reinan Rodrigues</div>
</body>
</html>
