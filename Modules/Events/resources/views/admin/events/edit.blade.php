@extends('admin::components.layouts.master')

@section('title', 'Editar: ' . $event->title . ' - Administração')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Evento</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events.events.show', $event) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="eye" class="w-4 h-4" /> Ver Evento
            </a>
            <a href="{{ route('admin.events.events.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" /> Voltar
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 p-4">
        <div class="flex items-center gap-3">
            <x-icon name="circle-check" style="duotone" class="w-5 h-5 text-green-500 flex-shrink-0" />
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

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

    <form action="{{ route('admin.events.events.update', $event) }}" method="POST" enctype="multipart/form-data"
          x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        @method('PUT')

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

            {{-- 8. Palestrantes (edit only) --}}
            @if($event->showSpeakersEnabled())
            @include('events::admin.events.partials._form_speakers')
            @endif

            {{-- 9. Programação (edit only) --}}
            @include('events::admin.events.partials._form_schedule')

            {{-- 10. Badge (edit only, se habilitado) --}}
            @if($event->hasBadgeEnabled())
            @include('events::admin.events.partials._form_badge')
            @endif

            {{-- 11. Certificado (edit only, se habilitado) --}}
            @if($event->hasCertificateEnabled())
            @include('events::admin.events.partials._form_certificate')
            @endif
        </div>

        {{-- Submit bar --}}
        <div class="sticky bottom-4 mt-6 flex justify-between items-center bg-white/90 dark:bg-gray-800/90 backdrop-blur rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg px-6 py-4">
            <div class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-2">
                <x-icon name="clock-rotate-left" class="w-3.5 h-3.5" />
                Última atualização: {{ $event->updated_at->diffForHumans() }}
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.events.events.show', $event) }}"
                   class="px-5 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </a>
                <button type="submit" :disabled="submitting"
                    class="px-6 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-colors flex items-center gap-2 disabled:opacity-70">
                    <x-icon name="floppy-disk" class="w-4 h-4" />
                    <span x-text="submitting ? 'Salvando...' : 'Salvar Alterações'"></span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
