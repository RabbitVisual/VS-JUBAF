{{--
    Partial: _form_speakers.blade.php
    Seção "Palestrantes" (edit only)
--}}
@php
    $ev = $event ?? null;
    $speakers = old('speakers', $ev?->speakers?->toArray() ?? []);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
     x-data="{
         speakers: {{ Js::from($speakers) }},
         addSpeaker() {
             this.speakers.push({ name: '', role: '', order: this.speakers.length });
         },
         removeSpeaker(idx) { this.speakers.splice(idx, 1); }
     }">

    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
        <div class="w-9 h-9 rounded-lg bg-teal-100 dark:bg-teal-900/40 flex items-center justify-center flex-shrink-0">
            <x-icon name="microphone-stand" style="duotone" class="w-5 h-5 text-teal-600 dark:text-teal-400" />
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Palestrantes / Convidados</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pessoas que irão se apresentar ou ministrar no evento</p>
        </div>
    </div>

    <div class="space-y-3">
        <template x-for="(speaker, idx) in speakers" :key="idx">
            <div class="rounded-lg border border-gray-200 dark:border-gray-600 p-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="hidden" :name="`speakers[${idx}][id]`" x-model="speaker.id">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nome</label>
                    <input type="text" :name="`speakers[${idx}][name]`" x-model="speaker.name"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm"
                        placeholder="Nome do palestrante">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Função / Título</label>
                    <input type="text" :name="`speakers[${idx}][role]`" x-model="speaker.role"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm"
                        placeholder="Ex: Pastor, Conferencista">
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Ordem</label>
                        <input type="number" :name="`speakers[${idx}][order]`" x-model="speaker.order" min="0"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm">
                    </div>
                    <button type="button" @click="removeSpeaker(idx)" class="text-red-400 hover:text-red-600 text-xs flex items-center gap-1 pb-1">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        </template>
        <button type="button" @click="addSpeaker()"
            class="w-full py-2.5 rounded-lg border border-dashed border-teal-300 dark:border-teal-700 text-xs font-medium text-teal-600 dark:text-teal-400 hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-900/20 transition-colors flex items-center justify-center gap-1.5">
            <x-icon name="plus" class="w-3.5 h-3.5" /> Adicionar Palestrante
        </button>
    </div>
</div>
