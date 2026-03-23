{{--
    Partial: _form_basic.blade.php
    Seção "Informações Básicas" do formulário de evento.
    Variáveis disponíveis:
        $event (nullable) — null em create, model em edit
--}}
@php $ev = $event ?? null; @endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="circle-info" style="duotone" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Informações Básicas</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Identidade e descrição do evento</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Title --}}
        <div class="md:col-span-2">
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Título do Evento <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" id="title" value="{{ old('title', $ev?->title) }}" required
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('title') border-red-300 @enderror"
                placeholder="Ex: Retiro de Jovens 2025 — Um Novo Tempo">
            @error('title') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Slug --}}
        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Slug (URL amigável)
            </label>
            <div class="flex rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                <span class="inline-flex items-center px-3 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs border-r border-gray-300 dark:border-gray-600 select-none whitespace-nowrap">/eventos/</span>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $ev?->slug) }}"
                    class="flex-1 rounded-none border-0 shadow-none focus:ring-0 dark:bg-gray-700 dark:text-white sm:text-sm"
                    placeholder="retiro-jovens-2025">
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deixe vazio para gerar automaticamente</p>
            @error('slug') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Event Type --}}
        <div>
            <label for="event_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tipo de Evento
            </label>
            <select name="event_type_id" id="event_type_id"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="">— Selecionar tipo —</option>
                @foreach($eventTypes ?? \Modules\Events\App\Models\EventType::orderBy('order')->get() as $type)
                    <option value="{{ $type->id }}" {{ old('event_type_id', $ev?->event_type_id) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            @error('event_type_id') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Descrição
            </label>
            <textarea name="description" id="description" rows="4"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="Descreva o evento, o tema, o objetivo e o que os participantes podem esperar...">{{ old('description', $ev?->description) }}</textarea>
            @error('description') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Target Audience --}}
        <div class="md:col-span-2" x-data="{ audiences: {{ Js::from(old('target_audience', $ev?->target_audience ?? [])) }} }">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Público-Alvo <span class="text-xs text-gray-400 font-normal">(selecione todos que se aplicam)</span>
            </label>
            @php
                $audienceOptions = \Modules\Events\App\Models\Event::getAudienceOptions();
            @endphp
            <div class="flex flex-wrap gap-2">
                @foreach($audienceOptions as $key => $label)
                    <label :class="audiences.includes('{{ $key }}') ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-400 dark:border-indigo-500 text-indigo-700 dark:text-indigo-300' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-indigo-300'"
                        class="relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border cursor-pointer transition-colors">
                        <input type="checkbox" name="target_audience[]" value="{{ $key }}" x-model="audiences" class="sr-only">
                        <span class="text-xs font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Será exibido na página do evento para ajudar os interessados a se identificarem</p>
        </div>

        {{-- Banner --}}
        <div>
            <label for="banner" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Banner / Capa
            </label>
            @if($ev?->banner_path)
                <div class="mb-2 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 h-24 bg-gray-50 dark:bg-gray-700">
                    <img src="{{ Storage::url($ev->banner_path) }}" alt="Banner atual" class="w-full h-full object-cover">
                </div>
                <label class="inline-flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" name="remove_banner" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-xs text-red-600 dark:text-red-400 font-medium"><x-icon name="trash" class="w-3.5 h-3.5 inline mr-1" /> Remover banner atual</span>
                </label>
            @endif
            <input type="file" name="banner" id="banner" accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-400">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recomendado: 1920×1080px. Max: 4MB</p>
            @error('banner') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Logo --}}
        <div>
            <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Logo do Evento
            </label>
            @if($ev?->logo_path)
                <div class="mb-2 w-16 h-16 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                    <img src="{{ Storage::url($ev->logo_path) }}" alt="Logo atual" class="w-full h-full object-contain">
                </div>
                <label class="inline-flex items-center gap-2 mb-2 cursor-pointer">
                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-xs text-red-600 dark:text-red-400 font-medium"><x-icon name="trash" class="w-3.5 h-3.5 inline mr-1" /> Remover logo atual</span>
                </label>
            @endif
            <input type="file" name="logo" id="logo" accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-400">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opcional. Exibido na barra do evento. Max: 2MB</p>
            @error('logo') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Ministry --}}
        @if(class_exists(\Modules\Ministries\App\Models\Ministry::class))
        <div>
            <label for="ministry_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ministério Responsável</label>
            <select name="ministry_id" id="ministry_id"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="">— Nenhum —</option>
                @foreach(\Modules\Ministries\App\Models\Ministry::orderBy('name')->get() as $m)
                    <option value="{{ $m->id }}" {{ old('ministry_id', $ev?->ministry_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Setlist --}}
        @if(class_exists(\Modules\Worship\App\Models\WorshipSetlist::class))
        <div>
            <label for="setlist_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Setlist de Louvor</label>
            <select name="setlist_id" id="setlist_id"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                <option value="">— Nenhum —</option>
                @foreach(\Modules\Worship\App\Models\WorshipSetlist::orderBy('scheduled_at', 'desc')->limit(100)->get() as $s)
                    <option value="{{ $s->id }}" {{ old('setlist_id', $ev?->setlist_id) == $s->id ? 'selected' : '' }}>
                        {{ $s->title }} ({{ $s->scheduled_at?->format('d/m/Y') }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif
    </div>
</div>
