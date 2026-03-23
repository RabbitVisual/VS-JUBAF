{{-- Theme: Corporativo / Imersão --}}
<div class="bg-gray-50 min-h-screen">
    {{-- Header Dark Corporativo --}}
    <div class="bg-[var(--color-secondary)] pt-32 pb-24 px-4 text-white relative min-h-[400px] flex items-center">
        @if($event->showCoverEnabled() && $event->banner_path)
            <div class="absolute inset-0 z-0">
                <img src="{{ Storage::url($event->banner_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover opacity-30">
                <div class="absolute inset-0 bg-gradient-to-r from-[var(--color-secondary)] via-[var(--color-secondary)]/80 to-transparent"></div>
            </div>
        @endif
        <div class="absolute inset-0 overflow-hidden opacity-10 z-0" style="background-image: radial-gradient(var(--color-main) 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="container mx-auto max-w-6xl relative z-10">
            <div class="flex flex-col md:flex-row gap-12 items-center">
                <div class="flex-1 space-y-6">
                    @if($event->eventType)
                        <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider bg-[var(--color-main)] text-white">
                            {{ $event->eventType->name }}
                        </span>
                    @endif

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight">{{ $event->title }}</h1>

                    <div class="flex flex-col sm:flex-row gap-6 text-gray-300 font-medium">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded bg-white/10 flex items-center justify-center">
                                <x-icon name="calendar-days" class="w-5 h-5 text-[var(--color-main)]" />
                            </div>
                            <div>
                                <div class="text-sm text-gray-400">Data e Hora</div>
                                <div class="text-white">{{ $event->start_date->format('d/m/Y - H:i') }}</div>
                            </div>
                        </div>
                        @if($event->showLocationEnabled() && $event->location)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded bg-white/10 flex items-center justify-center">
                                <x-icon name="location-dot" class="w-5 h-5 text-[var(--color-main)]" />
                            </div>
                            <div>
                                <div class="text-sm text-gray-400">Localização</div>
                                <div class="text-white">{{ $event->location }}</div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-4 pt-4" x-data="eventShare()">
                        <button type="button" @click="share()" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-white/10 hover:bg-white/20 text-white text-sm font-semibold transition-all border border-white/20">
                            <x-icon name="share-nodes" style="duotone" class="w-4 h-4 text-[var(--color-main)]" />
                            <span x-text="shared ? 'Obrigado!' : 'Compartilhar'"></span>
                        </button>
                        <button type="button" @click="copyLink()" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-white/10 hover:bg-white/20 text-white text-sm font-semibold transition-all border border-white/20">
                            <x-icon name="link" style="duotone" class="w-4 h-4 text-[var(--color-main)]" />
                            <span x-text="copied ? 'Link copiado!' : 'Copiar link'"></span>
                        </button>
                    </div>
                </div>

                <div class="w-full md:w-[400px] shrink-0">
                    <div class="bg-white p-6 rounded shadow-2xl text-gray-900 border-t-4" style="border-top-color: var(--color-main)">
                        <h3 class="font-bold text-xl mb-4">Inscreva-se Agora</h3>
                        <div class="space-y-4 mb-6">
                            @if($event->showCapacityEnabled() && $event->capacity > 0)
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between items-center text-sm font-medium">
                                    <span class="text-gray-500">Vagas Disponíveis</span>
                                    <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-800 text-xs">{{ $event->capacity - $event->total_participants }} restando</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full bg-[var(--color-main)] transition-all" style="width: {{ min(100, ($event->total_participants / $event->capacity) * 100) }}%"></div>
                                </div>
                            </div>
                            @endif
                            @if($event->registration_deadline)
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-gray-500">Encerramento</span>
                                <span class="text-red-600">{{ $event->registration_deadline->format('d/m/Y') }}</span>
                            </div>
                            @endif

                             @if(!$isFree && $hasBatches)
                                <div class="pt-4 border-t border-gray-100 space-y-3">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Lotes Disponíveis</p>
                                    @foreach($batches as $batch)
                                        @php $isSoldOut = $batch->quantity_available <= 0; @endphp
                                        <div class="flex justify-between items-center p-3 rounded border {{ $isSoldOut ? 'bg-gray-50 border-gray-100 opacity-50' : 'bg-white border-gray-200' }}">
                                            <div class="text-xs font-bold {{ $isSoldOut ? 'text-gray-400' : 'text-gray-900' }}">{{ $batch->name }}</div>
                                            <div class="text-sm font-black text-[var(--color-main)]">R$ {{ number_format($batch->price, 2, ',', '.') }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(isset($registrationConfig['use_segments']) && $registrationConfig['use_segments'] && !empty($registrationConfig['segments']))
                                <div class="pt-4 border-t border-gray-100 space-y-3">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Categorias de Inscrição</p>
                                    @foreach($registrationConfig['segments'] as $segment)
                                        <div class="flex justify-between items-center p-3 rounded border bg-gray-50 border-gray-100">
                                            <div class="text-xs font-bold text-gray-900">{{ $segment['label'] }}</div>
                                            <div class="text-sm font-black text-[var(--color-main)]">
                                                @if($isFree || empty($segment['price']) || (float)$segment['price'] <= 0) Grátis @else R$ {{ number_format($segment['price'], 2, ',', '.') }} @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if($event->is_active)
                            <button type="button" @click="registrationModalOpen = true" class="w-full py-4 text-white font-bold transition-opacity hover:opacity-90 rounded" style="background-color: var(--color-main)">
                                {{ (isset($registrationConfig['use_segments']) && $registrationConfig['use_segments']) ? 'Escolher Categoria e Inscrever-se' : ($isFree ? 'Realizar Inscrição Gratuita' : 'Inscrever-se no Evento') }}
                            </button>
                        @else
                            <div class="w-full py-4 bg-gray-200 text-gray-500 text-center font-bold rounded">
                                Inscrições Indisponíveis
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="container mx-auto max-w-6xl px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-12">
                @if($event->showAboutEnabled() && $event->description)
                <div id="sobre" class="scroll-mt-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1.5 h-6 bg-[var(--color-main)]"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Sobre o Evento</h2>
                    </div>
                    <div class="prose max-w-none text-gray-600">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>
                @endif

                @if($event->showScheduleEnabled() && $event->schedule && is_array($event->schedule) && count($event->schedule) > 0)
                <div id="programacao" class="scroll-mt-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1.5 h-6 bg-[var(--color-main)]"></div>
                        <h2 class="text-2xl font-bold text-gray-900">Agenda / Programação</h2>
                    </div>
                    <div class="bg-white border text-gray-700 divide-y divide-gray-100 shadow-sm">
                        @foreach($event->schedule as $item)
                            <div class="flex flex-col sm:flex-row gap-4 p-5 hover:bg-gray-50 transition-colors">
                                <div class="w-full sm:w-32 font-bold text-[var(--color-main)] flex-shrink-0">
                                    {{ $item['time'] ?? '--:--' }}
                                </div>
                                <div class="font-medium">{{ $item['title'] ?? $item['description'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($event->showMapEnabled() && is_array($event->location_data))
                    @php
                        $embedUrl = null;
                        if (!empty($event->location_data['lat']) && !empty($event->location_data['lng'])) {
                            $embedUrl = 'https://www.google.com/maps?q=' . rawurlencode($event->location_data['lat'] . ',' . $event->location_data['lng']) . '&output=embed';
                        } elseif (!empty($event->location_data['address'])) {
                            $embedUrl = 'https://www.google.com/maps?q=' . rawurlencode($event->location_data['address']) . '&output=embed';
                        }
                    @endphp
                    @if($embedUrl)
                        <div id="mapa" class="scroll-mt-24">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-1.5 h-6 bg-[var(--color-main)]"></div>
                                <h2 class="text-2xl font-bold text-gray-900">Localização no Mapa</h2>
                            </div>
                            <div class="rounded overflow-hidden border shadow-sm h-80">
                                <iframe src="{{ $embedUrl }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <div class="space-y-8">
                @if($event->showSpeakersEnabled() && $event->speakers->isNotEmpty())
                <div id="palestrantes" class="bg-white p-6 border shadow-sm scroll-mt-24">
                    <h3 class="font-bold text-lg text-gray-900 mb-4 border-b pb-2">Palestrantes / Convidados</h3>
                    <div class="space-y-4">
                        @foreach($event->speakers as $speaker)
                            <div class="flex items-center gap-3">
                                @if($speaker->photo_path)
                                    <img src="{{ Storage::url($speaker->photo_path) }}" class="w-12 h-12 object-cover rounded shadow-sm border border-gray-100">
                                @else
                                    <div class="w-12 h-12 bg-gray-100 flex items-center justify-center text-gray-400 rounded">
                                        <x-icon name="user" class="w-5 h-5" />
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-gray-900 text-sm">{{ $speaker->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $speaker->role }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($event->showContactEnabled() && ($event->contact_name || $event->contact_phone || $event->contact_email))
                <div class="bg-[var(--color-secondary)] text-white p-6 rounded shadow-sm">
                    <h3 class="font-bold text-lg mb-4 text-[var(--color-main)]">Dúvidas?</h3>
                    <p class="text-sm text-gray-300 mb-4">Entre em contato com nossa equipe de organização.</p>
                    <div class="space-y-3 text-sm">
                        @if($event->contact_name) <div class="flex items-center gap-2"><x-icon name="user" class="w-4 h-4 text-gray-400" /> {{ $event->contact_name }}</div> @endif
                        @if($event->contact_phone) <div class="flex items-center gap-2"><x-icon name="phone" class="w-4 h-4 text-gray-400" /> {{ $event->contact_phone }}</div> @endif
                        @if($event->contact_email) <div class="flex items-center gap-2"><x-icon name="envelope" class="w-4 h-4 text-gray-400" /> {{ $event->contact_email }}</div> @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
