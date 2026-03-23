@extends('memberpanel::components.layouts.master')

@section('title', $event->title . ' - ' . __('memberpanel::messages.member_panel'))

@push('head_scripts')
<style>
    :root {
        --color-main: {{ $event->theme_config['primary_color'] ?? '#4F46E5' }};
        --color-secondary: {{ $event->theme_config['secondary_color'] ?? '#111827' }};
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-16"
     x-data="{ registrationModalOpen: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 flex-wrap">
            <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Painel</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <a href="{{ route('memberpanel.events.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Eventos</a>
            <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
            <span class="text-gray-900 dark:text-white font-medium truncate max-w-[220px] sm:max-w-none">{{ $event->title }}</span>
        </nav>

        {{-- ════════════════════════════════════════════════════════════
             HERO SECTION
        ════════════════════════════════════════════════════════════ --}}
        <div class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-gray-100 dark:border-slate-800">
            {{-- Decorative glows --}}
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                <div class="absolute -top-24 -left-20 w-96 h-96 rounded-full blur-[120px] opacity-20" style="background: var(--color-main)"></div>
                <div class="absolute top-1/2 -right-20 w-80 h-80 rounded-full blur-[120px] opacity-15" style="background: var(--color-secondary)"></div>
            </div>

            @if($event->banner_path)
                <div class="absolute inset-0 z-0">
                    <img src="{{ Storage::url($event->banner_path) }}" alt="{{ $event->title }}"
                         class="w-full h-full object-cover opacity-20 dark:opacity-15">
                    <div class="absolute inset-0 bg-gradient-to-t from-white dark:from-slate-900 via-white/50 dark:via-slate-900/60 to-transparent"></div>
                </div>
            @endif

            <div class="relative z-10 px-6 sm:px-10 py-10 sm:py-14">
                {{-- Badges --}}
                <div class="flex flex-wrap items-center gap-2 mb-5">
                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest"
                          style="background: color-mix(in srgb, var(--color-main) 15%, transparent); color: var(--color-main)">
                        {{ $event->eventType?->name ?? 'Evento' }}
                    </span>
                    @if($event->is_active)
                        <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Inscrições Abertas
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-black uppercase tracking-widest">Encerrado</span>
                    @endif
                    @if($event->is_featured)
                        <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-1">
                            <x-icon name="star" class="w-3 h-3" /> Destaque
                        </span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-tight max-w-3xl mb-4">
                    {{ $event->title }}
                </h1>

                @if($event->description)
                    <p class="text-sm sm:text-base text-gray-600 dark:text-slate-300 leading-relaxed max-w-2xl mb-6">
                        {{ Str::limit(strip_tags($event->description), 280) }}
                    </p>
                @endif

                {{-- Meta Chips --}}
                <div class="flex flex-wrap gap-3 mb-8">
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200/70 dark:border-slate-700">
                        <x-icon name="calendar" class="w-4 h-4 text-indigo-500 shrink-0" />
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black uppercase text-gray-400 dark:text-slate-500 tracking-wider">Início</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $event->start_date->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    @if($event->end_date)
                        <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200/70 dark:border-slate-700">
                            <x-icon name="clock" class="w-4 h-4 text-purple-500 shrink-0" />
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase text-gray-400 dark:text-slate-500 tracking-wider">Término</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $event->end_date->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    @endif
                    @if($event->location)
                        <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200/70 dark:border-slate-700 max-w-xs">
                            <x-icon name="location-dot" class="w-4 h-4 text-pink-500 shrink-0" />
                            <div class="flex flex-col min-w-0">
                                <span class="text-[9px] font-black uppercase text-gray-400 dark:text-slate-500 tracking-wider">Local</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $event->location }}</span>
                            </div>
                        </div>
                    @endif
                    @if($event->showCapacityEnabled())
                        <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200/70 dark:border-slate-700">
                            <x-icon name="users" class="w-4 h-4 text-emerald-500 shrink-0" />
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase text-gray-400 dark:text-slate-500 tracking-wider">Vagas</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $event->total_participants ?? 0 }} / {{ $event->capacity > 0 ? $event->capacity : 'Ilimitadas' }}
                                </span>
                            </div>
                        </div>
                    @endif
                    @if($event->dress_code)
                        <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200/70 dark:border-slate-700">
                            <x-icon name="shirt" class="w-4 h-4 text-cyan-500 shrink-0" />
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase text-gray-400 dark:text-slate-500 tracking-wider">Traje</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $event->dress_code }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- CTA --}}
                @if($event->is_active)
                    <button type="button" @click="registrationModalOpen = true"
                            class="inline-flex items-center gap-3 px-8 py-4 text-white font-black rounded-2xl shadow-xl transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
                            style="background: linear-gradient(135deg, var(--color-main), var(--color-secondary))">
                        <x-icon name="circle-check" class="w-5 h-5" />
                        Inscrever-se Agora
                    </button>
                @else
                    <div class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-400 font-bold rounded-2xl text-sm">
                        <x-icon name="lock" class="w-4 h-4" />
                        Inscrições Encerradas
                    </div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════
             MAIN CONTENT GRID (Info + Sidebar)
        ════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT: Description + Schedule + Speakers + Map --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- About --}}
                @if($event->showAboutEnabled() && $event->description)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                                <x-icon name="circle-info" style="duotone" class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">Sobre o Evento</h2>
                        </div>
                        <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-slate-300 leading-relaxed">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>
                @endif

                {{-- Speakers --}}
                @if($event->showSpeakersEnabled() && $event->speakers->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                                <x-icon name="microphone" style="duotone" class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">Palestrantes</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($event->speakers as $speaker)
                                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700">
                                    @if($speaker->photo_path)
                                        <img src="{{ Storage::url($speaker->photo_path) }}" alt="{{ $speaker->name }}"
                                             class="w-14 h-14 rounded-full object-cover ring-2 shrink-0"
                                             style="ring-color: var(--color-main)">
                                    @else
                                        <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-black text-lg shrink-0"
                                             style="background: linear-gradient(135deg, var(--color-main), var(--color-secondary))">
                                            {{ strtoupper(substr($speaker->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 dark:text-white truncate">{{ $speaker->name }}</p>
                                        @if($speaker->role)
                                            <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $speaker->role }}</p>
                                        @endif
                                        @if($speaker->bio)
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $speaker->bio }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Schedule --}}
                @if($event->showScheduleEnabled() && !empty($event->schedule))
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                                <x-icon name="calendar-days" style="duotone" class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">Programação</h2>
                        </div>
                        <div class="space-y-3">
                            @foreach($event->schedule as $item)
                                <div class="flex gap-4 items-start p-4 bg-gray-50 dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700">
                                    @if(!empty($item['time']))
                                        <div class="shrink-0 w-16 text-center">
                                            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">{{ $item['time'] }}</span>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $item['title'] ?? '' }}</p>
                                        @if(!empty($item['description']))
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $item['description'] }}</p>
                                        @endif
                                        @if(!empty($item['speaker']))
                                            <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 flex items-center gap-1">
                                                <x-icon name="microphone" class="w-3 h-3" /> {{ $item['speaker'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Map --}}
                @if($event->showLocationEnabled() && $event->showMapEnabled())
                    @php
                        $locationData = is_array($event->location_data) ? $event->location_data : [];
                        $lat = $locationData['lat'] ?? null;
                        $lng = $locationData['lng'] ?? null;
                        $address = $locationData['formatted_address'] ?? $event->location ?? null;
                    @endphp
                    @if($address)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                            <div class="p-6 sm:p-8 pb-4">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-2 bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 rounded-xl">
                                        <x-icon name="location-dot" style="duotone" class="w-5 h-5" />
                                    </div>
                                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Localização</h2>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-slate-300 ml-11">{{ $address }}</p>
                            </div>
                            @if($lat && $lng)
                                <div class="h-52 w-full">
                                    <iframe
                                        width="100%" height="100%"
                                        frameborder="0" style="border:0; display:block;"
                                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                        src="https://maps.google.com/maps?q={{ $lat }},{{ $lng }}&z=15&output=embed"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            @else
                                <div class="h-52 w-full">
                                    <iframe
                                        width="100%" height="100%"
                                        frameborder="0" style="border:0; display:block;"
                                        loading="lazy"
                                        src="https://maps.google.com/maps?q={{ urlencode($address) }}&output=embed"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            @endif
                            <div class="px-6 py-3">
                                <a href="https://maps.google.com/?q={{ urlencode($address) }}" target="_blank"
                                   class="inline-flex items-center gap-2 text-xs font-bold text-pink-600 dark:text-pink-400 hover:underline">
                                    <x-icon name="arrow-up-right-from-square" class="w-3 h-3" />
                                    Abrir no Google Maps
                                </a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- RIGHT SIDEBAR --}}
            <div class="space-y-6">

                {{-- Registration CTA Card --}}
                @if($event->is_active)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-6">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-1">Garanta sua vaga</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-4">Inscrições disponíveis para membros.</p>

                        {{-- Price display --}}
                        @if($event->priceRules->isEmpty())
                            <div class="flex items-center gap-2 mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800">
                                <x-icon name="circle-check" class="w-5 h-5 text-emerald-600 shrink-0" />
                                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-300">Evento Gratuito</span>
                            </div>
                        @else
                            <div class="space-y-2 mb-4">
                                @foreach($event->priceRules->take(3) as $rule)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600 dark:text-slate-300">{{ $rule->label }}</span>
                                        <span class="font-black text-gray-900 dark:text-white">
                                            R$ {{ number_format($rule->price, 2, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Segments summary --}}
                        @if(!empty($registrationConfig['segments']))
                            <div class="space-y-2 mb-4">
                                @foreach($registrationConfig['segments'] as $seg)
                                    <div class="flex justify-between items-center text-xs p-2 bg-gray-50 dark:bg-slate-800 rounded-lg">
                                        <span class="text-gray-600 dark:text-slate-400">{{ $seg['label'] }}</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $seg['quantity'] }} vagas</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <button type="button" @click="registrationModalOpen = true"
                                class="w-full py-4 text-white font-black rounded-2xl shadow-lg transition-all hover:opacity-90 active:scale-[0.98]"
                                style="background: linear-gradient(135deg, var(--color-main), var(--color-secondary))">
                            <x-icon name="circle-check" class="w-5 h-5 inline mr-2" />
                            Inscrever-se
                        </button>

                        @if($event->registration_deadline)
                            <p class="text-xs text-center text-gray-400 dark:text-slate-500 mt-3">
                                Prazo: {{ $event->registration_deadline->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Event Details Card --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-6 space-y-4">
                    <h3 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-wide">Detalhes</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                                <x-icon name="calendar" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Data</p>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $event->start_date->format('d/m/Y') }}</p>
                                @if($event->end_date && $event->end_date->format('d/m/Y') !== $event->start_date->format('d/m/Y'))
                                    <p class="text-gray-500 dark:text-slate-400">até {{ $event->end_date->format('d/m/Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                                <x-icon name="clock" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Horário</p>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $event->start_date->format('H:i') }}
                                    @if($event->end_date) até {{ $event->end_date->format('H:i') }} @endif
                                </p>
                            </div>
                        </div>

                        @if($event->location)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-pink-50 dark:bg-pink-900/30 flex items-center justify-center shrink-0">
                                    <x-icon name="location-dot" class="w-4 h-4 text-pink-600 dark:text-pink-400" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Local</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $event->location }}</p>
                                </div>
                            </div>
                        @endif

                        @if($event->showAudienceEnabled() && !empty($event->target_audience))
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                                    <x-icon name="users" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Público-alvo</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $event->audience_display }}</p>
                                </div>
                            </div>
                        @endif

                        @if($event->dress_code)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center shrink-0">
                                    <x-icon name="shirt" class="w-4 h-4 text-cyan-600 dark:text-cyan-400" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Traje</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $event->dress_code }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Contact Card --}}
                @if($event->showContactEnabled() && ($event->contact_name || $event->contact_email || $event->contact_phone || $event->contact_whatsapp))
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-6 space-y-4">
                        <h3 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-wide">Contato & Dúvidas</h3>
                        <div class="space-y-3 text-sm">
                            @if($event->contact_name)
                                <p class="font-bold text-gray-900 dark:text-white">{{ $event->contact_name }}</p>
                            @endif
                            @if($event->contact_email)
                                <a href="mailto:{{ $event->contact_email }}" class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <x-icon name="envelope" class="w-4 h-4 shrink-0" />
                                    {{ $event->contact_email }}
                                </a>
                            @endif
                            @if($event->contact_whatsapp)
                                <a href="https://wa.me/55{{ preg_replace('/\D/', '', $event->contact_whatsapp) }}" target="_blank"
                                   class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:underline">
                                    <x-icon name="whatsapp" style="brands" class="w-4 h-4 shrink-0" />
                                    {{ $event->contact_whatsapp }}
                                </a>
                            @elseif($event->contact_phone)
                                <a href="tel:{{ $event->contact_phone }}" class="flex items-center gap-2 text-gray-600 dark:text-slate-300 hover:underline">
                                    <x-icon name="phone" class="w-4 h-4 shrink-0" />
                                    {{ $event->contact_phone }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- My Registrations Link --}}
                <a href="{{ route('memberpanel.events.my-registrations') }}"
                   class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors group">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 transition-colors shrink-0">
                        <x-icon name="ticket" style="duotone" class="w-5 h-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 dark:text-white text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Minhas Inscrições</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Ver inscrições confirmadas</p>
                    </div>
                    <x-icon name="chevron-right" class="w-4 h-4 text-gray-400 dark:text-slate-600 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all shrink-0" />
                </a>

            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         REGISTRATION MODAL (Reuses the proven 3-step wizard partial)
    ════════════════════════════════════════════════════════════ --}}
    @if($event->is_active)
        @include('events::public.partials.registration-modal', [
            'event'              => $event,
            'registrationConfig' => $registrationConfig ?? ['use_segments' => false, 'form_fields' => $event->form_fields ?? []],
            'isFree'             => $event->isFree(),
            'hasBatches'         => $event->hasBatches(),
            'batches'            => $event->batches()->get(),
            'gateways'           => $gateways ?? collect(),
            'defaultParticipant' => $defaultParticipant ?? null,
            'registrationAction' => route('memberpanel.events.register', $event),
        ])
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('eventShare', () => ({
        shared: false,
        copied: false,
        url: @json(url()->current()),
        share() {
            const text = `Evento: {{ $event->title }}\nData: {{ $event->start_date->format('d/m/Y H:i') }}\n\nInscreva-se: ${this.url}`;
            window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
            this.shared = true; setTimeout(() => this.shared = false, 2000);
        },
        copyLink() {
            navigator.clipboard.writeText(this.url).then(() => {
                this.copied = true; setTimeout(() => this.copied = false, 2000);
            });
        }
    }));
});
</script>
@endpush
