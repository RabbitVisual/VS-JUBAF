<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Remote Projeção - VertexCBAV</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-pro/css/all.css') }}">
    @vite(['resources/css/app.css', 'Modules/Projection/resources/assets/js/remote.js'])
</head>
<body class="bg-slate-950 text-white antialiased min-h-screen" x-data="projectionRemote()">
    <div class="max-w-lg mx-auto pb-6 px-3">
        <header class="py-4 border-b border-slate-800">
            <div class="flex items-center justify-between">
                <h1 class="text-sm font-black uppercase tracking-widest text-indigo-400 flex items-center gap-2">
                    <i class="fa-duotone fa-mobile-screen"></i> Remote
                </h1>
                <span class="text-[10px] font-bold text-slate-500 truncate max-w-[140px]" x-text="liveTitle || '—'"></span>
            </div>
            <div class="flex items-center gap-3 mt-1.5 text-[10px] font-bold text-slate-500" x-show="slideLabel || itemLabel">
                <span x-text="slideLabel" x-show="slideLabel"></span>
                <span x-show="slideLabel && itemLabel">·</span>
                <span x-text="itemLabel" x-show="itemLabel"></span>
            </div>
        </header>
        <div x-show="errorMessage" class="mt-2 py-2 px-3 rounded-lg bg-red-500/20 border border-red-500/50 text-red-400 text-xs font-bold" x-text="errorMessage"></div>
        <div x-show="loading" class="mt-2 py-1 text-slate-500 text-[10px] font-bold flex items-center gap-2">
            <i class="fa-duotone fa-spinner-third fa-spin"></i> Enviando...
        </div>

        <!-- Master buttons: Clear, Blackout, Logo -->
        <div class="grid grid-cols-3 gap-2 py-4">
            <button @click="sendState({ isClear: true, type: 'clear', content: '' })"
                    class="h-14 rounded-xl bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white font-black text-[10px] uppercase tracking-widest transition flex flex-col items-center justify-center gap-0.5">
                <i class="fa-duotone fa-eraser text-lg"></i> Clear
            </button>
            <button @click="sendState({ isBlackout: !state.isBlackout })"
                    :class="state.isBlackout ? 'bg-black text-red-500 border border-red-900/50' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                    class="h-14 rounded-xl font-black text-[10px] uppercase tracking-widest transition flex flex-col items-center justify-center gap-0.5 border border-transparent">
                <i class="fa-duotone fa-power-off text-lg"></i> Black
            </button>
            <button @click="sendState({ type: 'logo', isClear: false, isBlackout: false })"
                    class="h-14 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white font-black text-[10px] uppercase tracking-widest transition flex flex-col items-center justify-center gap-0.5">
                <i class="fa-duotone fa-church text-lg"></i> Logo
            </button>
        </div>

        <!-- Quick countdown & stage alerts -->
        <div class="grid grid-cols-4 gap-2 pb-2">
            <button @click="sendState({ type: 'countdown', countdown_seconds: 300, footer: '5 min', isClear: false, isBlackout: false })" class="h-10 rounded-lg bg-slate-800 hover:bg-amber-600 text-slate-300 text-[10px] font-black uppercase">5 min</button>
            <button @click="sendState({ type: 'countdown', countdown_seconds: 60, footer: '1 min', isClear: false, isBlackout: false })" class="h-10 rounded-lg bg-slate-800 hover:bg-amber-600 text-slate-300 text-[10px] font-black uppercase">1 min</button>
            <button @click="sendState({ alertMessage: '5 MINUTOS' })" class="h-10 rounded-lg bg-slate-800 hover:bg-red-600 text-slate-300 text-[10px] font-black uppercase">Alerta 5m</button>
            <button @click="sendState({ alertMessage: 'SILÊNCIO' })" class="h-10 rounded-lg bg-slate-800 hover:bg-red-600 text-slate-300 text-[10px] font-black uppercase">Silêncio</button>
        </div>

        <!-- Next / Prev slide -->
        <div class="grid grid-cols-2 gap-2 pb-4">
            <button @click="prevSlide()" class="h-12 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-300 font-black text-[10px] uppercase tracking-widest transition flex items-center justify-center gap-2">
                <i class="fa-duotone fa-chevron-left"></i> Anterior
            </button>
            <button @click="nextSlide()" class="h-12 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-300 font-black text-[10px] uppercase tracking-widest transition flex items-center justify-center gap-2">
                Próximo <i class="fa-duotone fa-chevron-right"></i>
            </button>
        </div>

        <!-- Setlist selector: carregar ordem do culto para ver timeline -->
        <div class="py-2">
            <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Ordem do culto</h2>
            <select @change="onSetlistSelect($event.target.value)"
                    class="w-full bg-slate-800 border border-slate-700 rounded-lg text-sm text-white px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none">
                <option value="">Carregar setlist...</option>
                <template x-for="s in setlists" :key="s.id">
                    <option :value="s.id" :selected="setlistData && setlistData.id == s.id" x-text="formatSetlistLabel(s)"></option>
                </template>
            </select>
        </div>

        <!-- Schedule (timeline) — toque no item para selecionar e enviar ao vivo -->
        <div class="py-2" x-show="setlistItems.length">
            <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Timeline</h2>
            <div class="space-y-1 max-h-40 overflow-y-auto rounded-lg border border-slate-800 p-2">
                <template x-for="(item, idx) in setlistItems" :key="item.id">
                    <button type="button" @click="goToItem(item)"
                            :class="['w-full flex items-center gap-2 p-2 rounded-lg text-left text-xs cursor-pointer transition border border-transparent', currentItemId == item.id ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-slate-800/50 text-slate-400 hover:bg-slate-700/70 border-slate-700']">
                        <span class="w-5 h-5 rounded bg-black/20 flex items-center justify-center font-black text-[10px] shrink-0" x-text="idx + 1"></span>
                        <span class="truncate flex-1" x-text="item.title"></span>
                        <span x-show="currentItemId == item.id" class="text-[9px] font-black text-white/80 shrink-0">LIVE</span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Clickable slides of current item -->
        <div class="py-2" x-show="currentSlides.length">
            <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Slides — toque para enviar ao vivo</h2>
            <div class="grid grid-cols-4 gap-2">
                <template x-for="(slide, idx) in currentSlides" :key="idx">
                    <button @click="goToSlide(idx)"
                            :class="['p-3 rounded-xl text-left text-[10px] font-bold border transition min-h-[60px]', currentSlideIndex === idx ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-800 border-slate-700 text-slate-300 hover:border-slate-600']">
                        <span x-text="idx + 1"></span>
                        <div class="mt-1 line-clamp-2 text-[9px] opacity-80" x-html="slide.html ? slide.html.replace(/<[^>]+>/g,'').slice(0,30) : ''"></div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Themes -->
        <div class="py-2" x-show="themes.length">
            <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tema</h2>
            <select @change="onThemeChange($event)" class="w-full bg-slate-800 border border-slate-700 rounded-lg text-sm text-white px-3 py-2 focus:ring-1 focus:ring-indigo-500 outline-none">
                <template x-for="t in themes" :key="t.id || t.slug">
                    <option :value="t.slug || t.id" :selected="(state.theme === (t.slug || t.id)) || state.theme_id == t.id" x-text="t.name"></option>
                </template>
            </select>
        </div>
    </div>
</body>
</html>
