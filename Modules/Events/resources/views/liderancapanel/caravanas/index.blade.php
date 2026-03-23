@extends('liderancapanel::components.layouts.master')

@section('title', 'Minha Caravana')

@section('content')
    <div class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Minha Caravana JUBAF</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Gestão local da juventude da sua igreja nos eventos da associação.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse ($events as $event)
                <a href="{{ route('lideranca.caravanas.show', $event) }}"
                    class="group block rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                            {{ $event->start_date?->format('d/m/Y') ?? 'Sem data' }}
                        </span>
                        <span class="text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 px-2.5 py-0.5 rounded-full">
                            {{ $event->caravan_registrations_count ?? 0 }} inscritos
                        </span>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                        {{ $event->title }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ $event->location ?: 'Local a definir' }}
                    </p>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-12 text-center">
                    <div class="mx-auto mb-3 w-14 h-14 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                        <x-icon name="calendar-days" class="w-7 h-7 text-gray-400 dark:text-slate-300" />
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Sua igreja ainda não possui caravanas ativas</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Quando houver inscrições nos eventos, elas aparecerão aqui.</p>
                </div>
            @endforelse
        </div>

        @if ($events->hasPages())
            <div>
                {{ $events->links() }}
            </div>
        @endif
    </div>
@endsection
