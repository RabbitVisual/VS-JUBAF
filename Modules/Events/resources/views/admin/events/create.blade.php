@extends('admin::components.layouts.master')

@section('title', __('events::messages.create_event') . ' - Administração')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Criar Novo Evento</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Preencha as informações para criar um evento profissional completo</p>
        </div>
        <a href="{{ route('admin.events.events.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" /> Voltar
        </a>
    </div>

    {{-- Flash messages --}}
    @if($errors->any())
    <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-4">
        <div class="flex items-start gap-3">
            <x-icon name="triangle-exclamation" style="duotone" class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" />
            <div>
                <p class="text-sm font-semibold text-red-800 dark:text-red-300">Corrija os erros abaixo</p>
                <ul class="mt-1 text-xs text-red-700 dark:text-red-400 space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.events.events.store') }}" method="POST" enctype="multipart/form-data"
          x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div class="space-y-6">
            {{-- 1. Informações Básicas --}}
            @include('events::admin.events.partials._form_basic')

            {{-- 2. Data, Local e Restrições --}}
            @include('events::admin.events.partials._form_date_location')

            {{-- 3. Status, Visibilidade e Contato --}}
            @include('events::admin.events.partials._form_contact')

            {{-- 4. Formulário de Inscrição --}}
            @include('events::admin.events.partials._form_registration')

            {{-- 5. Regras de Preço --}}
            @include('events::admin.events.partials._form_price_rules')

            {{-- 6. Configuração de Tema e Aparência --}}
            @include('events::admin.events.partials._form_appearance')

            {{-- 7. Opções de Página e Recursos --}}
            @include('events::admin.events.partials._form_options')
        </div>

        {{-- Submit bar --}}
        <div class="sticky bottom-4 mt-6 flex justify-end gap-3 bg-white/80 dark:bg-gray-800/80 backdrop-blur rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg px-6 py-4">
            <a href="{{ route('admin.events.events.index') }}"
               class="px-5 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancelar
            </a>
            <button type="submit" name="status_submit" value="draft"
                class="px-5 py-2 rounded-lg border border-indigo-300 dark:border-indigo-700 text-sm text-indigo-700 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors flex items-center gap-2">
                <x-icon name="floppy-disk" class="w-4 h-4" /> Salvar como Rascunho
            </button>
            <button type="submit" :disabled="submitting"
                class="px-6 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-colors flex items-center gap-2 disabled:opacity-70">
                <x-icon name="check" class="w-4 h-4" />
                <span x-text="submitting ? 'Salvando...' : 'Publicar Evento'"></span>
            </button>
        </div>
    </form>
</div>
@endsection
