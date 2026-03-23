@extends('admin::components.layouts.master')

@section('title', 'Gerenciar Carousel')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gerenciar Carousel</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Adicione, edite e ordene os slides da página inicial. <span class="text-sm text-gray-500">{{ $slides->count() }} slide(s), {{ $slides->where('is_active', true)->count() }} ativo(s).</span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('homepage.index') }}" target="_blank" rel="noopener"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors">
                    <x-icon name="eye" class="w-5 h-5" />
                    Preview
                </a>
                <a href="{{ route('admin.homepage.carousel.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 transition-colors">
                    <x-icon name="plus" class="w-5 h-5" />
                    Novo Slide
                </a>
            </div>
        </div>

        <!-- Feedback Messages -->
        @if (session('success'))
            <div class="p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg border border-green-200 dark:border-green-800">
                {{ session('success') }}
            </div>
        @endif

        <!-- Slides List (Sortable) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Slides Ativos</h2>
                    <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Arraste para ordenar</span>
                </div>
            </div>

            <div id="sortable-slides" class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($slides as $slide)
                    <div class="slide-item group p-4 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" data-id="{{ $slide->id }}">
                        <!-- Drag Handle -->
                        <div class="cursor-move text-gray-400 hover:text-gray-600 p-2">
                            <x-icon name="grip-vertical" class="w-6 h-6" />
                        </div>

                        <!-- Thumbnail -->
                        <div class="relative w-32 h-20 bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 flex-shrink-0">
                            @if($slide->image_url)
                                <img src="{{ $slide->image_url }}" alt="{{ $slide->alt_text }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <x-icon name="image" class="w-8 h-8" />
                                </div>
                            @endif
                            <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs px-2 py-1 truncate">
                                {{ $slide->title ?? 'Sem Título' }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $slide->title ?? 'Sem Título' }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                {{ $slide->description ?? 'Sem descrição' }}
                            </p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $slide->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $slide->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                                @if($slide->starts_at || $slide->ends_at)
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <x-icon name="clock" class="w-3 h-3" />
                                    Agendado
                                </span>
                                @endif
                                @if($slide->link)
                                <a href="{{ $slide->link }}" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                    <x-icon name="link" class="w-3 h-3" />
                                    Link
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                             <button onclick="toggleActive({{ $slide->id }})"
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Alternar Status">
                                <x-icon name="power-off" class="w-5 h-5" />
                             </button>
                            <form action="{{ route('admin.homepage.carousel.duplicate', $slide) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors" title="Duplicar slide">
                                    <x-icon name="copy" class="w-5 h-5" />
                                </button>
                            </form>
                            <a href="{{ route('admin.homepage.carousel.edit', $slide) }}"
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Editar">
                                <x-icon name="pen-to-square" class="w-5 h-5" />
                            </a>
                            <form action="{{ route('admin.homepage.carousel.destroy', $slide) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este slide?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Excluir">
                                    <x-icon name="trash-can" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <x-icon name="images" class="w-12 h-12 mx-auto text-gray-400 mb-3" />
                        <p class="text-lg font-medium">Nenhum slide encontrado</p>
                        <p class="text-sm">Comece adicionando seu primeiro slide ao carousel.</p>
                        <a href="{{ route('admin.homepage.carousel.create') }}" class="mt-4 inline-block text-blue-600 hover:underline">Criar Slide</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Scripts for Sortable & Toggles -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var el = document.getElementById('sortable-slides');
            if(el) {
                var sortable = Sortable.create(el, {
                    animation: 150,
                    handle: '.cursor-move',
                    onEnd: function() {
                        var order = [];
                        document.querySelectorAll('#sortable-slides .slide-item').forEach(function(item, index) {
                            order.push({
                                id: item.getAttribute('data-id'),
                                order: index + 1
                            });
                        });

                        // Send order to backend
                        fetch("{{ route('admin.homepage.carousel.order') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ slides: order })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                // Optional: Show toast
                                console.log('Order updated');
                            }
                        });
                    }
                });
            }
        });

        function toggleActive(id) {
            fetch(`/admin/homepage/carousel/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.reload(); // Simple reload to reflect state, can be optimized later
                }
            });
        }
    </script>
@endsection

