{{-- Theme: Moderno (Padrão Original do Sistema) --}}
{{-- Hero --}}
<div class="relative w-full min-h-[55vh] flex items-end pb-12 overflow-hidden pt-24">
    @if($event->showCoverEnabled() && $event->banner_path)
        <div class="absolute inset-0">
            <img src="{{ Storage::url($event->banner_path) }}" alt="{{ $event->title }}" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-transparent"></div>
        </div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-[var(--color-main)] via-slate-900 to-[var(--color-secondary)] opacity-80"></div>
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_30%_50%,_rgba(255,255,255,0.08)_0%,_transparent_50%)]"></div>
    @endif

    <div class="relative z-10 w-full container mx-auto px-4 max-w-7xl">
        @if($event->eventType)
            <span class="inline-block px-3 py-1 mb-3 text-xs font-semibold tracking-wider text-[var(--color-main)] bg-[var(--color-main)]/20 rounded-full border border-[var(--color-main)]/40">
                {{ $event->eventType->name }}
            </span>
        @endif
        <span class="inline-block px-3 py-1 mb-3 text-xs font-semibold tracking-wider text-[var(--color-main)] uppercase border border-[var(--color-main)] rounded-full bg-[var(--color-main)]/10">
            {{ $event->is_active ? (__('events::messages.tickets_available') ?? 'Ingressos disponíveis') : (__('events::messages.event_ended') ?? 'Evento encerrado') }}
        </span>
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-white mb-4 drop-shadow-lg">
            {{ $event->title }}
        </h1>
        <div class="flex flex-wrap items-center gap-4 text-slate-300 text-base md:text-lg mb-4">
            <span class="flex items-center gap-2">
                <x-icon name="calendar-days" style="duotone" class="w-5 h-5 text-[var(--color-main)]" />
                {{ $event->start_date->format('d/m/Y') }} às {{ $event->start_date->format('H:i') }}
                @if($event->end_date)
                    <span class="text-slate-400">— até {{ $event->end_date->format('d/m/Y') }}</span>
                @endif
            </span>
            @if($event->showLocationEnabled() && $event->location)
                <span class="flex items-center gap-2">
                    <x-icon name="location-dot" style="duotone" class="w-5 h-5 text-[var(--color-main)]" />
                    {{ $event->location }}
                </span>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-3" x-data="eventShare()">
            <button type="button" @click="share()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-medium transition-colors border border-white/20">
                <x-icon name="share-nodes" style="duotone" class="w-5 h-5" />
                <span x-text="shared ? 'Obrigado!' : 'Compartilhar'"></span>
            </button>
            <button type="button" @click="copyLink()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-medium transition-colors border border-white/20">
                <x-icon name="link" style="duotone" class="w-5 h-5" />
                <span x-text="copied ? 'Link copiado!' : 'Copiar link'"></span>
            </button>
        </div>
    </div>
</div>

{{-- Content: two columns --}}
<div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12 -mt-8 relative z-20">
    {{-- Left: About, Schedule, Speakers, Location, Map, Capacity --}}
    <div class="lg:col-span-2 space-y-8">
        @if($event->showAboutEnabled() && $event->description)
            <div id="sobre" class="bg-slate-900/80 backdrop-blur rounded-2xl p-8 border border-slate-800 scroll-mt-24">
                <h2 class="text-2xl font-bold text-[var(--color-main)] border-l-4 pl-4 mb-4 flex items-center gap-2" style="border-left-color: var(--color-main)">
                    <x-icon name="circle-info" style="duotone" class="w-6 h-6" />
                    {{ __('events::messages.about_event') ?? 'Sobre o evento' }}
                </h2>
                <div class="text-slate-300 leading-relaxed prose prose-invert max-w-none">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>
        @endif

        @if($event->showScheduleEnabled() && $event->schedule && is_array($event->schedule) && count($event->schedule) > 0)
            <div id="programacao" class="bg-slate-900/80 backdrop-blur rounded-2xl p-8 border border-slate-800 scroll-mt-24">
                <h2 class="text-2xl font-bold text-[var(--color-main)] border-l-4 pl-4 mb-4 flex items-center gap-2" style="border-left-color: var(--color-main)">
                    <x-icon name="list-check" style="duotone" class="w-6 h-6" />
                    {{ __('events::messages.schedule_heading') }}
                </h2>
                <ul class="space-y-3">
                    @foreach($event->schedule as $item)
                        <li class="flex gap-4 items-start text-slate-300">
                            @if(!empty($item['time']))
                                <span class="font-mono font-semibold text-[var(--color-main)] shrink-0">{{ $item['time'] }}</span>
                            @endif
                            <span>{{ $item['title'] ?? $item['description'] ?? '' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($event->showSpeakersEnabled() && $event->speakers->isNotEmpty())
            <div id="palestrantes" class="bg-slate-900/80 backdrop-blur rounded-2xl p-8 border border-slate-800 scroll-mt-24">
                <h2 class="text-2xl font-bold text-[var(--color-main)] border-l-4 pl-4 mb-4 flex items-center gap-2" style="border-left-color: var(--color-main)">
                    <x-icon name="microphone" style="duotone" class="w-6 h-6" />
                    {{ __('events::messages.speakers_heading') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($event->speakers as $speaker)
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-800/50 border border-slate-700">
                            @if($speaker->photo_path)
                                <img src="{{ Storage::url($speaker->photo_path) }}" alt="{{ $speaker->name }}" class="w-14 h-14 rounded-full object-cover">
                            @else
                                <div class="w-14 h-14 rounded-full flex items-center justify-center text-[var(--color-main)]" style="background-color: color-mix(in srgb, var(--color-main) 20%, transparent);">
                                    <x-icon name="user" style="duotone" class="w-7 h-7" />
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-white">{{ $speaker->name }}</p>
                                @if($speaker->role)
                                    <p class="text-sm text-slate-400">{{ $speaker->role }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($event->showLocationEnabled() && $event->location)
            <div id="local" class="bg-slate-900/80 backdrop-blur rounded-2xl p-8 border border-slate-800 scroll-mt-24">
                <h2 class="text-2xl font-bold text-[var(--color-main)] border-l-4 pl-4 mb-4 flex items-center gap-2" style="border-left-color: var(--color-main)">
                    <x-icon name="location-dot" style="duotone" class="w-6 h-6" />
                    {{ __('events::messages.location') ?? 'Local' }}
                </h2>
                <p class="font-medium text-white mb-2">{{ $event->location }}</p>
                @php
                    $mapsLink = is_array($event->location_data) ? ($event->location_data['maps_url'] ?? $event->location_data['link'] ?? null) : null;
                    if (!$mapsLink && is_array($event->location_data) && !empty($event->location_data['address'])) {
                        $mapsLink = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($event->location_data['address']);
                    }
                @endphp
                @if($mapsLink)
                    <a href="{{ $mapsLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-[var(--color-main)] hover:text-white font-medium transition-colors">
                        <x-icon name="map-location-dot" style="duotone" class="w-5 h-5" />
                        {{ __('events::messages.get_directions') ?? 'Como chegar' }}
                    </a>
                @endif
            </div>
        @endif

        @if($event->showMapEnabled() && is_array($event->location_data) && (!empty($event->location_data['address']) || (!empty($event->location_data['lat']) && !empty($event->location_data['lng']))))
            @php
                $embedUrl = null;
                if (!empty($event->location_data['lat']) && !empty($event->location_data['lng'])) {
                    $embedUrl = 'https://www.google.com/maps?q=' . rawurlencode($event->location_data['lat'] . ',' . $event->location_data['lng']) . '&output=embed';
                } elseif (!empty($event->location_data['address'])) {
                    $embedUrl = 'https://www.google.com/maps?q=' . rawurlencode($event->location_data['address']) . '&output=embed';
                }
            @endphp
            @if($embedUrl)
                <div id="mapa" class="bg-slate-900/80 backdrop-blur rounded-2xl overflow-hidden border border-slate-800 scroll-mt-24">
                    <div class="p-4 border-b border-slate-800">
                        <h2 class="text-2xl font-bold text-[var(--color-main)] border-l-4 pl-4 flex items-center gap-2" style="border-left-color: var(--color-main)">
                            <x-icon name="map-location-dot" style="duotone" class="w-6 h-6" />
                            {{ __('events::messages.map') ?? 'Mapa' }}
                        </h2>
                    </div>
                    <iframe src="{{ $embedUrl }}" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full"></iframe>
                </div>
            @endif
        @endif

        @if($event->showCapacityEnabled())
        <div id="vagas" class="bg-slate-900/80 backdrop-blur rounded-2xl p-8 border border-slate-800 scroll-mt-24">
            <h2 class="text-xl font-bold text-[var(--color-main)] border-l-4 pl-4 mb-4 flex items-center gap-2" style="border-left-color: var(--color-main)">
                <x-icon name="users" style="duotone" class="w-6 h-6" />
                {{ __('events::messages.capacity') ?? 'Vagas' }}
            </h2>
            @if($event->capacity > 0)
                <div class="w-full bg-slate-700 rounded-full h-2.5 mb-2 min-w-[120px]">
                    <div class="h-2.5 rounded-full transition-all bg-[var(--color-main)]" style="width: {{ min(100, ($event->total_participants / $event->capacity) * 100) }}%"></div>
                </div>
                <p class="text-slate-300 font-medium">{{ $event->total_participants }} de {{ $event->capacity }} vagas</p>
            @else
                <p class="font-bold text-white">{{ __('events::messages.unlimited_capacity') ?? 'Vagas ilimitadas' }}</p>
            @endif
        </div>
        @endif
    </div>

    {{-- Right: Sticky CTA + Batches --}}
    <div class="lg:col-span-1">
        <div class="sticky top-24 bg-slate-900 rounded-2xl p-6 border border-slate-800 shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <x-icon name="ticket" style="duotone" class="w-5 h-5 text-[var(--color-main)]" />
                {{ $hasBatches ? (__('events::messages.choose_ticket') ?? 'Escolha seu ingresso') : (__('events::messages.ensure_spot') ?? 'Garantir vaga') }}
            </h3>

            @if($event->is_active)
                @if($isFree)
                    <button type="button" @click="registrationModalOpen = true" class="inline-flex items-center justify-center gap-2 w-full py-4 bg-[var(--color-main)] hover:bg-[var(--color-secondary)] text-white font-bold rounded-xl transition-colors">
                        <x-icon name="circle-check" style="duotone" class="w-5 h-5" />
                        {{ (isset($registrationConfig['use_segments']) && $registrationConfig['use_segments']) ? 'Escolher Categoria e Inscrever-se' : (__('events::messages.register') ?? 'Inscreva-se') }}
                    </button>
                @else
                    @if($hasBatches)
                        <p class="text-slate-400 text-sm mb-4">{{ __('events::messages.choose_batch_then_continue') ?? 'Escolha o lote e preencha os dados na próxima tela.' }}</p>
                        <button type="button" @click="registrationModalOpen = true" class="inline-flex items-center justify-center gap-2 w-full py-4 bg-[var(--color-main)] hover:bg-[var(--color-secondary)] text-white font-bold rounded-xl transition-colors mb-6">
                            <x-icon name="arrow-right" style="duotone" class="w-5 h-5" />
                            {{ __('events::messages.continue_to_register') ?? 'Continuar para inscrição' }}
                        </button>
                        <div class="space-y-4">
                            @forelse($batches as $batch)
                                @php $isSoldOut = $batch->quantity_available <= 0; @endphp
                                <div class="p-4 rounded-xl border-2 transition-all {{ $isSoldOut ? 'bg-slate-950 border-slate-800 opacity-60' : 'bg-slate-800 border-slate-700' }}">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-bold {{ $isSoldOut ? 'text-slate-500' : 'text-white' }}">{{ $batch->name }}</span>
                                        @if($isSoldOut)
                                            <span class="px-2 py-0.5 text-xs font-bold text-slate-400 bg-slate-800 rounded">ESGOTADO</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-bold text-[#10b981] bg-[#10b981]/10 rounded">DISPONÍVEL</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between items-baseline">
                                        @if($batch->end_date)
                                            <span class="text-sm text-slate-400">{{ __('events::messages.sales_until') ?? 'Vendas até' }} {{ $batch->end_date->format('d/m') }}</span>
                                        @else
                                            <span></span>
                                        @endif
                                        <span class="text-xl font-bold text-[var(--color-main)]">R$ {{ number_format($batch->price, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    @endif

                    @if(isset($registrationConfig['use_segments']) && $registrationConfig['use_segments'] && !empty($registrationConfig['segments']))
                        <div class="mt-4 space-y-4">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">{{ __('events::messages.registration_categories') ?? 'Categorias de Inscrição' }}</p>
                            @foreach($registrationConfig['segments'] as $segment)
                                <div class="p-4 rounded-xl border-2 bg-slate-800 border-slate-700 transition-all">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-bold text-white">{{ $segment['label'] }}</span>
                                        <span class="px-2 py-0.5 text-xs font-bold text-[#10b981] bg-[#10b981]/10 rounded">DISPONÍVEL</span>
                                    </div>
                                    <div class="flex justify-between items-baseline">
                                        <span class="text-sm text-slate-400">{{ $segment['description'] ?? '' }}</span>
                                        <span class="text-xl font-bold text-[var(--color-main)]">
                                            @if($isFree || empty($segment['price']) || (float)$segment['price'] <= 0) Grátis @else R$ {{ number_format($segment['price'], 2, ',', '.') }} @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                            @if(!$hasBatches)
                                <button type="button" @click="registrationModalOpen = true" class="inline-flex items-center justify-center gap-2 w-full py-4 bg-[var(--color-main)] hover:bg-[var(--color-secondary)] text-white font-bold rounded-xl transition-colors mt-4">
                                    <x-icon name="circle-check" style="duotone" class="w-5 h-5" />
                                    {{ __('events::messages.choose_category_and_register') ?? 'Escolher Categoria e Inscrever-se' }}
                                </button>
                            @endif
                        </div>
                    @endif

                    @if(!$hasBatches && !(isset($registrationConfig['use_segments']) && $registrationConfig['use_segments']))
                        <button type="button" @click="registrationModalOpen = true" class="inline-flex items-center justify-center gap-2 w-full py-4 bg-[var(--color-main)] hover:bg-[var(--color-secondary)] text-white font-bold rounded-xl transition-colors">
                            <x-icon name="ticket" style="duotone" class="w-5 h-5" />
                            {{ __('events::messages.ensure_spot') ?? 'Garantir minha vaga' }}
                        </button>
                    @endif
                @endif
            @else
                <div class="py-4 text-center rounded-xl bg-slate-800 text-slate-400 font-medium">
                    {{ __('events::messages.registrations_closed') ?? 'Inscrições encerradas' }}
                </div>
            @endif

            @if($event->showContactEnabled() && ($event->contact_name || $event->contact_phone || $event->contact_email))
                <div class="mt-6 p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <h4 class="text-white font-bold mb-4 flex items-center gap-2">
                        <x-icon name="circle-question" style="duotone" class="w-5 h-5 text-[var(--color-main)]" />
                        Dúvidas sobre o evento?
                    </h4>
                    <div class="space-y-3">
                        @if($event->contact_name)
                            <div class="flex items-center gap-3 text-sm text-slate-300">
                                <x-icon name="user" class="w-4 h-4 opacity-50" />
                                <span>{{ $event->contact_name }}</span>
                            </div>
                        @endif
                        @if($event->contact_phone)
                            <div class="flex items-center gap-3 text-sm text-slate-300">
                                <x-icon name="phone" class="w-4 h-4 opacity-50" />
                                <span>{{ $event->contact_phone }}</span>
                            </div>
                        @endif
                        @if($event->contact_email)
                            <div class="flex items-center gap-3 text-sm text-slate-300">
                                <x-icon name="envelope" class="w-4 h-4 opacity-50" />
                                <span>{{ $event->contact_email }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
