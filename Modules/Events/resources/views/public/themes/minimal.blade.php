{{-- Theme: Minimalista --}}

    <div class="bg-white dark:bg-gray-950 min-h-screen">
        {{-- Hero Header Minimal --}}
        <div class="pt-32 pb-16 px-4">
            <div class="container mx-auto max-w-5xl text-center">
                @if($event->eventType)
                    <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-widest uppercase text-[var(--color-main)] bg-[var(--color-main)]/10 rounded-full">
                        {{ $event->eventType->name }}
                    </span>
                @endif

                <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-8">{{ $event->title }}</h1>

                <div class="flex flex-wrap justify-center items-center gap-x-8 gap-y-4 text-lg text-gray-600 dark:text-gray-400 font-medium mb-12">
                    @if($event->showLocationEnabled() && $event->location)
                    <span class="flex items-center gap-2">
                        <x-icon name="location-dot" class="w-5 h-5 text-gray-400" />
                        {{ $event->location }}
                    </span>
                    @endif
                </div>

                <div class="flex justify-center items-center gap-4 mb-12" x-data="eventShare()">
                    <button type="button" @click="share()" class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium transition-all">
                        <x-icon name="share-nodes" style="duotone" class="w-4 h-4 text-[var(--color-main)]" />
                        <span x-text="shared ? 'Obrigado!' : 'Compartilhar'"></span>
                    </button>
                    <button type="button" @click="copyLink()" class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium transition-all">
                        <x-icon name="link" style="duotone" class="w-4 h-4 text-[var(--color-main)]" />
                        <span x-text="copied ? 'Link copiado!' : 'Copiar link'"></span>
                    </button>
                </div>

                @if($event->showCoverEnabled() && $event->banner_path)
                    <div class="rounded-3xl overflow-hidden shadow-2xl relative aspect-[21/9] max-w-4xl mx-auto mb-16 ring-1 ring-gray-900/5">
                        <img src="{{ Storage::url($event->banner_path) }}" alt="Capa" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="max-w-xl mx-auto mb-16">
                    @if($event->is_active)
                        @if(!$isFree && $hasBatches)
                            <div class="space-y-4 mb-8 text-left">
                                @forelse($batches as $batch)
                                    @php $isSoldOut = $batch->quantity_available <= 0; @endphp
                                    <div class="p-4 rounded-2xl border transition-all {{ $isSoldOut ? 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-800 opacity-60' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 hover:shadow-md' }}">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold {{ $isSoldOut ? 'text-gray-500' : 'text-gray-900 dark:text-white' }}">{{ $batch->name }}</span>
                                            @if($isSoldOut)
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Esgotado</span>
                                            @else
                                                <span class="text-xl font-black text-[var(--color-main)]">R$ {{ number_format($batch->price, 2, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        @if($batch->end_date && !$isSoldOut)
                                            <div class="text-xs text-gray-500">Vendas até {{ $batch->end_date->format('d/m/Y') }}</div>
                                        @endif
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        @endif

                        @if(isset($registrationConfig['use_segments']) && $registrationConfig['use_segments'] && !empty($registrationConfig['segments']))
                            <div class="space-y-4 mb-8 text-left">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('events::messages.registration_categories') ?? 'Categorias de Inscrição' }}</p>
                                @foreach($registrationConfig['segments'] as $segment)
                                    <div class="p-4 rounded-2xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 hover:shadow-md transition-all">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $segment['label'] }}</span>
                                            <span class="text-xl font-black text-[var(--color-main)]">
                                                @if($isFree || empty($segment['price']) || (float)$segment['price'] <= 0) Grátis @else R$ {{ number_format($segment['price'], 2, ',', '.') }} @endif
                                            </span>
                                        </div>
                                        @if(!empty($segment['description']))
                                            <div class="text-xs text-gray-500">{{ $segment['description'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <button type="button" @click="registrationModalOpen = true" class="w-full py-5 px-8 rounded-full bg-[var(--color-main)] hover:bg-[var(--color-secondary)] text-white text-xl font-bold transition-all shadow-xl hover:-translate-y-1">
                            @if(isset($registrationConfig['use_segments']) && $registrationConfig['use_segments'])
                                Escolher Categoria e Inscrever-se
                            @else
                                {{ $isFree ? 'Inscreva-se Gratuitamente' : 'Garantir Ingresso' }}
                            @endif
                        </button>
                    @else
                        <div class="w-full py-5 px-8 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 font-bold text-xl cursor-not-allowed">
                            Inscrições Encerradas
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-900 pb-24">
            <div class="container mx-auto max-w-4xl px-4 pt-16">

                @if($event->showAboutEnabled() && $event->description)
                <div id="sobre" class="mb-16 scroll-mt-24">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Sobre</h2>
                    <div class="prose prose-lg dark:prose-invert prose-p:text-gray-600 dark:prose-p:text-gray-400 max-w-none">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                     <div class="space-y-12">
                        @if($event->showScheduleEnabled() && $event->schedule && is_array($event->schedule) && count($event->schedule) > 0)
                            <div id="programacao" class="scroll-mt-24">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Programação</h3>
                                <div class="space-y-6">
                                    @foreach($event->schedule as $item)
                                        <div class="flex gap-4">
                                            <div class="text-[var(--color-main)] font-mono font-bold">{{ $item['time'] ?? '' }}</div>
                                            <div class="text-gray-700 dark:text-gray-300">{{ $item['title'] ?? $item['description'] ?? '' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($event->showSpeakersEnabled() && $event->speakers->isNotEmpty())
                            <div id="palestrantes" class="scroll-mt-24">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Palestrantes</h3>
                                <div class="space-y-6">
                                    @foreach($event->speakers as $speaker)
                                        <div class="flex items-center gap-4">
                                            @if($speaker->photo_path)
                                                <img src="{{ Storage::url($speaker->photo_path) }}" class="w-16 h-16 rounded-full object-cover">
                                            @else
                                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                                    <x-icon name="user" class="w-6 h-6 text-gray-400" />
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $speaker->name }}</div>
                                                <div class="text-gray-500 text-sm">{{ $speaker->role }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                     </div>

                     <div class="space-y-8">
                         <div id="local" class="bg-gray-50 dark:bg-gray-900 p-8 rounded-3xl scroll-mt-24">
                             <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Informações Gerais</h3>
                             <ul class="space-y-5 text-gray-600 dark:text-gray-400">
                                @if($event->showLocationEnabled() && $event->location)
                                <li class="flex gap-3">
                                    <x-icon name="location-dot" class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" />
                                    <span>{{ $event->location }}</span>
                                </li>
                                @endif
                                @if($event->showCapacityEnabled() && $event->capacity > 0)
                                <li class="flex gap-3">
                                    <x-icon name="users" class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium">{{ $event->capacity - $event->total_participants }} vagas restantes</span>
                                            <span class="text-[10px] font-bold text-gray-400">{{ round(($event->total_participants / $event->capacity) * 100) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-1.5">
                                            <div class="bg-[var(--color-main)] h-1.5 rounded-full" style="width: {{ min(100, ($event->total_participants / $event->capacity) * 100) }}%"></div>
                                        </div>
                                    </div>
                                </li>
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
                                        <li class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
                                            <div class="rounded-2xl overflow-hidden h-48 ring-1 ring-gray-200 dark:ring-gray-800 mb-2">
                                                <iframe src="{{ $embedUrl }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" class="filter grayscale contrast-125 dark:invert dark:hue-rotate-180"></iframe>
                                            </div>
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($event->location) }}" target="_blank" class="text-xs font-bold text-[var(--color-main)] hover:underline flex items-center gap-1">
                                                <x-icon name="map-location-dot" class="w-3 h-3" /> Ver no Google Maps
                                            </a>
                                        </li>
                                    @endif
                                @endif
                                @if($event->registration_deadline)
                                <li class="flex gap-3">
                                    <x-icon name="clock" class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" />
                                    <span>Inscrições até {{ $event->registration_deadline->format('d/m/Y') }}</span>
                                </li>
                                @endif
                                @if($event->showContactEnabled() && ($event->contact_name || $event->contact_phone || $event->contact_email))
                                    <li class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Dúvidas?</div>
                                        <div class="space-y-2">
                                            @if($event->contact_name) <div class="text-sm font-medium text-gray-900 dark:text-white flex items-center gap-2"><x-icon name="user" class="w-3 h-3 text-gray-400" /> {{ $event->contact_name }}</div> @endif
                                            @if($event->contact_phone) <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2"><x-icon name="phone" class="w-3 h-3 text-gray-400" /> {{ $event->contact_phone }}</div> @endif
                                            @if($event->contact_email) <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2"><x-icon name="envelope" class="w-3 h-3 text-gray-400" /> {{ $event->contact_email }}</div> @endif
                                        </div>
                                    </li>
                                @endif
                             </ul>
                         </div>
                     </div>
                </div>

            </div>
        </div>
    </div>
