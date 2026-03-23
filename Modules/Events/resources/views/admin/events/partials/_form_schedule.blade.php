{{--
    Partial: _form_schedule.blade.php
    Seção "Programação" do evento
--}}
@php
    $ev = $event ?? null;
    $schedule = old('schedule', $ev?->schedule ?? []);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
     x-data="{
         schedule: {{ Js::from($schedule) }},
         addItem() {
             this.schedule.push({ time: '', title: '', description: '' });
         },
         removeItem(idx) { this.schedule.splice(idx, 1); }
     }">

    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
        <div class="w-9 h-9 rounded-lg bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="list-timeline" style="duotone" class="w-5 h-5 text-sky-600 dark:text-sky-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Programação</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Cronograma de atividades do evento</p>
        </div>
    </div>

    <div class="space-y-3">
        <template x-for="(item, idx) in schedule" :key="idx">
            <div class="rounded-lg border border-gray-200 dark:border-gray-600 p-3 grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Horário</label>
                    <input type="text" :name="`schedule[${idx}][time]`" x-model="item.time"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm"
                        placeholder="08:00">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Título / Atividade</label>
                    <input type="text" :name="`schedule[${idx}][title]`" x-model="item.title"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm"
                        placeholder="Ex: Abertura com Louvor">
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Descrição</label>
                        <input type="text" :name="`schedule[${idx}][description]`" x-model="item.description"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm"
                            placeholder="Opcional">
                    </div>
                    <button type="button" @click="removeItem(idx)" class="text-red-400 hover:text-red-600 pb-1">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        </template>
        <button type="button" @click="addItem()"
            class="w-full py-2.5 rounded-lg border border-dashed border-sky-300 dark:border-sky-700 text-xs font-medium text-sky-600 dark:text-sky-400 hover:border-sky-500 hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-colors flex items-center justify-center gap-1.5">
            <x-icon name="plus" class="w-3.5 h-3.5" /> Adicionar Item
        </button>
    </div>
</div>
