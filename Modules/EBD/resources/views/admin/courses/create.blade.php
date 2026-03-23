@extends('admin::components.layouts.master')

@section('title', 'Novo Curso EBD - Administração')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500" x-data="courseWizard()">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white rounded">Currículo</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">EBD · Wizard</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Novo <span class="text-blue-600">Curso</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Assistente em 3 passos: dados básicos, configuração e revisão.</p>
        </div>
        <a href="{{ route('admin.ebd.courses.index') }}" class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
            <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
            Voltar
        </a>
    </div>

    <!-- Step indicator -->
    <div class="flex items-center justify-center gap-2 sm:gap-4">
        <template x-for="(s, i) in steps" :key="i">
            <div class="flex items-center">
                <button type="button" @click="if (i < step) step = i + 1" class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-2xl font-black text-sm transition-all"
                    :class="step === i + 1 ? 'bg-blue-600 text-white shadow-lg' : (step > i + 1 ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-400')">
                    <span x-text="i + 1"></span>
                </button>
                <span class="ml-2 text-xs font-bold text-gray-500 dark:text-gray-400 hidden sm:inline" x-text="s"></span>
                <template x-if="i < steps.length - 1">
                    <x-icon name="chevron-right" class="w-4 h-4 mx-1 text-gray-300 dark:text-gray-600" />
                </template>
            </div>
        </template>
    </div>

    <form action="{{ route('admin.ebd.courses.store') }}" method="POST" id="course-wizard-form" class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden"
        onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando curso...' } }))">
        @csrf

        <!-- Step 1: Dados Básicos -->
        <div x-show="step === 1" x-transition class="p-8 space-y-8">
            <h2 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                <x-icon name="pen-fancy" style="duotone" class="w-5 h-5 text-blue-600" />
                Dados básicos
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nome do curso</label>
                    <input type="text" name="name" x-model="name" required placeholder="Ex: Panorama do Antigo Testamento"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Slug (opcional)</label>
                    <input type="text" name="slug" x-model="slug" placeholder="gerado automaticamente"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Ordem</label>
                    <input type="number" name="order" x-model.number="order" min="0"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Descrição</label>
                    <textarea name="description" x-model="description" rows="3" placeholder="Breve descrição do currículo"
                        class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button type="button" @click="step = 2" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg">
                    Próximo: Configuração
                    <x-icon name="arrow-right" style="duotone" class="w-4 h-4 inline-block ml-2" />
                </button>
            </div>
        </div>

        <!-- Step 2: Configuração de Matrícula / Ministério -->
        <div x-show="step === 2" x-transition x-cloak class="p-8 space-y-8">
            <h2 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                <x-icon name="users-viewfinder" style="duotone" class="w-5 h-5 text-blue-600" />
                Configuração
            </h2>
            <div class="rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-6">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    As turmas deste curso podem ser vinculadas ao <strong>Ministério de Educação Cristã</strong> (ou EBD). 
                    Ao criar cada turma, você definirá o ministério e as regras de matrícula. Aqui apenas ativamos o curso.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" :value="is_active ? '1' : '0'">
                <input type="checkbox" x-model="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Curso ativo (visível para turmas)</label>
            </div>
            <div class="flex justify-between pt-4">
                <button type="button" @click="step = 1" class="px-6 py-3 text-gray-500 hover:text-gray-900 dark:hover:text-white font-bold text-sm transition-colors flex items-center gap-2">
                    <x-icon name="arrow-left" style="duotone" class="w-4 h-4" />
                    Voltar
                </button>
                <button type="button" @click="step = 3" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg">
                    Próximo: Revisão
                    <x-icon name="arrow-right" style="duotone" class="w-4 h-4 inline-block ml-2" />
                </button>
            </div>
        </div>

        <!-- Step 3: Upload de Materiais / Revisão -->
        <div x-show="step === 3" x-transition x-cloak class="p-8 space-y-8">
            <h2 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                <x-icon name="folder-open" style="duotone" class="w-5 h-5 text-blue-600" />
                Revisão e criar
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Materiais (PDFs, vídeos) são adicionados por <strong>lição</strong> após criar o curso. Revise os dados e finalize.</p>
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-6 space-y-4">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nome</span>
                    <p class="font-bold text-gray-900 dark:text-white" x-text="name || '—'"></p>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Descrição</span>
                    <p class="text-sm text-gray-600 dark:text-gray-300" x-text="description || 'Nenhuma'"></p>
                </div>
                <div class="flex gap-4">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Ordem</span>
                        <p class="font-bold text-gray-900 dark:text-white" x-text="order"></p>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status</span>
                        <p class="font-bold" :class="is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500'" x-text="is_active ? 'Ativo' : 'Inativo'"></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                <button type="button" @click="step = 2" class="px-6 py-3 text-gray-500 hover:text-gray-900 dark:hover:text-white font-bold text-sm transition-colors flex items-center gap-2">
                    <x-icon name="arrow-left" style="duotone" class="w-4 h-4" />
                    Voltar
                </button>
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all shadow-lg flex items-center gap-2">
                    <x-icon name="check" style="duotone" class="w-4 h-4" />
                    Criar curso
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function courseWizard() {
    return {
        step: 1,
        steps: ['Dados básicos', 'Configuração', 'Revisão'],
        name: {!! json_encode(old('name', '')) !!},
        slug: {!! json_encode(old('slug', '')) !!},
        description: {!! json_encode(old('description', '')) !!},
        order: {{ (int) old('order', 0) }},
        is_active: true
    };
}
</script>
@endpush
@endsection
