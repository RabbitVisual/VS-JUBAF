{{--
    Partial: _form_fields_builder.blade.php
    Componente reutilizável de builder de campos extras.
    Parâmetros:
        $fieldPrefix   — prefixo do name: 'form_fields' ou 'registration_segments[N][form_fields]'
        $existingFields — array de campos existentes (pode ser vazio)
--}}
@php
    $fieldPrefix    = $fieldPrefix ?? 'form_fields';
    $existingFields = $existingFields ?? [];
    $fieldTypes     = [
        'text'     => 'Texto curto',
        'textarea' => 'Texto longo',
        'number'   => 'Número',
        'email'    => 'E-mail',
        'phone'    => 'Telefone',
        'date'     => 'Data',
        'select'   => 'Seleção (lista)',
        'radio'    => 'Escolha única',
        'checkbox' => 'Múltipla escolha',
        'url'      => 'Link / URL',
    ];
    $alpineId = Str::random(6);
@endphp

<div x-data="{
    fields: {{ Js::from($existingFields) }},
    addField() {
        this.fields.push({ type: 'text', name: '', label: '', placeholder: '', help_text: '', required: false, options: [] });
    },
    removeField(idx) {
        this.fields.splice(idx, 1);
    },
    addOption(field) {
        if (!field.options) field.options = [];
        field.options.push('');
    },
    removeOption(field, oidx) {
        field.options.splice(oidx, 1);
    }
}" class="space-y-3">

    <div class="flex items-center justify-between mb-2">
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">
            <x-icon name="plus-circle" class="w-3.5 h-3.5 inline mr-1" />
            Campos Extras Personalizados
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500">Adicione perguntas adicionais para este formulário</p>
    </div>

    <template x-for="(field, idx) in fields" :key="idx">
        <div class="rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
            {{-- Field header --}}
            <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 dark:bg-gray-700 text-xs">
                <span class="font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <x-icon name="grip-lines" class="w-3.5 h-3.5 text-gray-400 cursor-move" />
                    Campo <span x-text="idx + 1"></span>:
                    <span class="text-violet-600 dark:text-violet-400 font-semibold" x-text="field.label || '(sem nome)'"></span>
                </span>
                <button type="button" @click="removeField(idx)" class="text-red-400 hover:text-red-600 transition-colors">
                    <x-icon name="xmark" class="w-4 h-4" />
                </button>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Type --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo</label>
                    <select :name="`{{ $fieldPrefix }}[${idx}][type]`" x-model="field.type"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm">
                        @foreach($fieldTypes as $ftKey => $ftLabel)
                            <option value="{{ $ftKey }}">{{ $ftLabel }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Label --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Rótulo do Campo *</label>
                    <input type="text" :name="`{{ $fieldPrefix }}[${idx}][label]`" x-model="field.label" required
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                        placeholder="Ex: Tamanho da camiseta">
                </div>

                {{-- Name (slug) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nome (chave interna)</label>
                    <input type="text" :name="`{{ $fieldPrefix }}[${idx}][name]`" x-model="field.name"
                        @input="field.name = field.name.toLowerCase().replace(/[^a-z0-9_]/g, '_')"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm font-mono"
                        placeholder="tamanho_camiseta">
                </div>

                {{-- Placeholder --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Placeholder</label>
                    <input type="text" :name="`{{ $fieldPrefix }}[${idx}][placeholder]`" x-model="field.placeholder"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                        placeholder="Texto de exemplo...">
                </div>

                {{-- Help text --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Texto de Ajuda</label>
                    <input type="text" :name="`{{ $fieldPrefix }}[${idx}][help_text]`" x-model="field.help_text"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                        placeholder="Explicação adicional para o participante...">
                </div>

                {{-- Required toggle --}}
                <div class="sm:col-span-2 flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" :name="`{{ $fieldPrefix }}[${idx}][required]`" x-model="field.required" value="1"
                            class="rounded border-gray-300 text-violet-600 focus:ring-violet-500 dark:border-gray-600 dark:bg-gray-700">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Campo obrigatório</span>
                    </label>
                    <span class="text-xs text-gray-400">|</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="field.required ? '❗ Participante deve preencher' : 'Participante pode deixar vazio'"></span>
                </div>

                {{-- Options for select/radio/checkbox --}}
                <div class="sm:col-span-2" x-show="['select','radio','checkbox'].includes(field.type)">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Opções de resposta</label>
                    <div class="space-y-1.5">
                        <template x-for="(opt, oidx) in field.options" :key="oidx">
                            <div class="flex gap-2">
                                <input type="text" :name="`{{ $fieldPrefix }}[${idx}][options][]`" x-model="field.options[oidx]"
                                    class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs shadow-sm"
                                    placeholder="Opção de resposta">
                                <button type="button" @click="removeOption(field, oidx)" class="text-red-400 hover:text-red-600 text-xs px-2">✕</button>
                            </div>
                        </template>
                        <button type="button" @click="addOption(field)"
                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 font-medium flex items-center gap-1 mt-1">
                            <x-icon name="plus" class="w-3.5 h-3.5" /> Adicionar opção
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <button type="button" @click="addField()"
        class="w-full py-2.5 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 text-xs font-medium text-gray-500 dark:text-gray-400 hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors flex items-center justify-center gap-1.5">
        <x-icon name="plus" class="w-3.5 h-3.5" />
        Adicionar Campo Personalizado
    </button>
</div>
