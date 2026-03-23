@extends('memberpanel::layouts.master')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Meus Bens</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Itens sob sua responsabilidade</p>
        </div>

        @if($pendingTerms->count() > 0)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="exclamation-circle" class="h-5 w-5 text-yellow-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Atenção: Termos pendentes de assinatura
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                             <p>Você possui {{ $pendingTerms->count() }} termo(s) de responsabilidade aguardando sua assinatura/confirmação.</p>
                             <ul class="list-disc list-inside mt-1">
                                 @foreach($pendingTerms as $term)
                                     <li>
                                         {{ $term->asset->name }} ({{ $term->type == 'loan' ? 'Empréstimo' : 'Cessão' }})
                                         <form action="{{ route('assets.memberpanel.my-assets.sign', $term->id) }}" method="POST" class="inline ml-2">
                                             @csrf
                                             <button type="submit" class="text-blue-600 underline hover:text-blue-800 font-bold">Confirmar Recebimento</button>
                                         </form>
                                     </li>
                                 @endforeach
                             </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($assets as $asset)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group hover:border-blue-200 dark:hover:border-blue-800 transition-colors">
                    <div class="h-48 bg-gray-100 dark:bg-gray-700 relative">
                        @if($asset->photo_path)
                            <img src="{{ asset($asset->photo_path) }}" alt="{{ $asset->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <x-icon name="photograph" class="w-12 h-12" />
                            </div>
                        @endif
                        <div class="absolute top-2 right-2">
                            <span class="inline-flex px-2 py-1 text-xs font-bold rounded-full bg-white/90 text-gray-800 shadow-sm backdrop-blur-sm">
                                {{ $asset->code }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-2">
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{ $asset->category->name }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $asset->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">
                            {{ $asset->description }}
                        </p>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                             <div class="text-xs text-gray-500">
                                 Recebido em: {{ $asset->movements->last()->date->format('d/m/Y') ?? '-' }}
                             </div>
                             <!-- Actions if any, like 'Report Issue' -->
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                    <x-icon name="check-circle" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nenhum bem sob sua responsabilidade</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        Você não possui itens alocados para seu uso no momento.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

