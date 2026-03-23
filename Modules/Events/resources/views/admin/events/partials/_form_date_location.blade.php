{{--
    Partial: _form_date_location.blade.php
    Seção "Data, Local e Restrições"
    $ev = $event ?? null
--}}
@php $ev = $event ?? null; @endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="calendar-days" style="duotone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Data, Local e Restrições</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Quando, onde e condições de participação</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Start Date --}}
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Data / Hora de Início <span class="text-red-500">*</span>
            </label>
            <input type="datetime-local" name="start_date" id="start_date" required
                value="{{ old('start_date', $ev?->start_date?->format('Y-m-d\TH:i')) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('start_date') border-red-300 @enderror">
            @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- End Date --}}
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Data / Hora de Término
            </label>
            <input type="datetime-local" name="end_date" id="end_date"
                value="{{ old('end_date', $ev?->end_date?->format('Y-m-d\TH:i')) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm @error('end_date') border-red-300 @enderror">
            @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Registration Deadline --}}
        <div>
            <label for="registration_deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Prazo para Inscrições
            </label>
            <input type="datetime-local" name="registration_deadline" id="registration_deadline"
                value="{{ old('registration_deadline', $ev?->registration_deadline?->format('Y-m-d\TH:i')) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Após essa data as inscrições serão fechadas automaticamente</p>
        </div>

        {{-- Recurrence --}}
        <div>
            <label for="recurrence_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Recorrência
            </label>
            <select name="recurrence_type" id="recurrence_type"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
                @foreach(\Modules\Events\App\Models\Event::getRecurrenceOptions() as $key => $label)
                    <option value="{{ $key }}" {{ old('recurrence_type', $ev?->recurrence_type ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Location --}}
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Local / Nome do Espaço
            </label>
            <input type="text" name="location" id="location"
                value="{{ old('location', $ev?->location) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="Ex: Acampamento Monte Carmelo">
        </div>

        {{-- Full Address --}}
        <div>
            <label for="location_data_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Endereço Completo
            </label>
            <input type="text" name="location_data[address]" id="location_data_address"
                value="{{ old('location_data.address', $ev?->location_data['address'] ?? '') }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="Rua X, 123 — Bairro — Cidade/UF">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Usado no link "Como chegar" e no mapa</p>
        </div>

        {{-- Lat/Lng --}}
        <div>
            <label for="location_data_lat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
            <input type="text" name="location_data[lat]" id="location_data_lat"
                value="{{ old('location_data.lat', $ev?->location_data['lat'] ?? '') }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="-12.3456">
        </div>
        <div>
            <label for="location_data_lng" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
            <input type="text" name="location_data[lng]" id="location_data_lng"
                value="{{ old('location_data.lng', $ev?->location_data['lng'] ?? '') }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="-38.9876">
        </div>

        {{-- Capacity --}}
        <div>
            <label for="capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Capacidade Total de Vagas
            </label>
            <input type="number" name="capacity" id="capacity" min="1"
                value="{{ old('capacity', $ev?->capacity) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="Deixe vazio para ilimitado">
        </div>

        {{-- Max per Registration --}}
        <div>
            <label for="max_per_registration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Máx. Participantes por Inscrição
            </label>
            <input type="number" name="max_per_registration" id="max_per_registration" min="1" max="100"
                value="{{ old('max_per_registration', $ev?->max_per_registration ?? 10) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Uma família pode inscrever até N pessoas de uma vez</p>
        </div>

        {{-- Age restrictions --}}
        <div>
            <label for="min_age_restriction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Idade Mínima para Participar
            </label>
            <input type="number" name="min_age_restriction" id="min_age_restriction" min="0" max="120"
                value="{{ old('min_age_restriction', $ev?->min_age_restriction) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="Sem restrição">
        </div>
        <div>
            <label for="max_age_restriction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Idade Máxima para Participar
            </label>
            <input type="number" name="max_age_restriction" id="max_age_restriction" min="0" max="120"
                value="{{ old('max_age_restriction', $ev?->max_age_restriction) }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                placeholder="Sem restrição">
        </div>

        {{-- Dress Code --}}
        <div class="md:col-span-2" x-data="{ selectedDress: '{{ old('dress_code', $ev?->dress_code ?? '') }}' }">
            <label for="dress_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Código de Vestimenta
            </label>
            <div class="flex flex-wrap gap-2">
                <label :class="selectedDress === '' ? 'bg-gray-100 dark:bg-gray-600 border-gray-400 text-gray-800 dark:text-gray-200' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'"
                    class="relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border cursor-pointer transition-colors">
                    <input type="radio" name="dress_code" value="" x-model="selectedDress" class="sr-only">
                    <span class="text-xs font-medium">Não definido</span>
                </label>
                @foreach(\Modules\Events\App\Models\Event::getDressCodeOptions() as $key => $label)
                    <label :class="selectedDress === '{{ $key }}' ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-400 text-blue-700 dark:text-blue-300' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-blue-300'"
                        class="relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border cursor-pointer transition-colors">
                        <input type="radio" name="dress_code" value="{{ $key }}" x-model="selectedDress" class="sr-only">
                        <span class="text-xs font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</div>
