<div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-xl shadow-red-500/5 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-sm font-black uppercase tracking-widest text-red-600 dark:text-red-400">Intercessão Urgente</h3>
        <a href="{{ route('member.intercessor.room.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 dark:hover:text-gray-300">Ver Todos</a>
    </div>
    <div class="p-4 space-y-4">
        @forelse($urgentRequests as $request)
            <div class="flex gap-4 items-start">
                <div class="shrink-0">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                        <x-icon name="exclamation" class="w-5 h-5" />
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <a href="{{ route('member.intercessor.room.show', $request->id) }}" class="block group">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate group-hover:text-blue-600 transition-colors">
                            {{ $request->title }}
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mt-1">
                            {{ $request->description }}
                        </p>
                    </a>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">
                            {{ $request->created_at->diffForHumans() }}
                        </span>
                        @if($request->urgency_level === 'critical')
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                Prioridade Extrema
                            </span>
                        @elseif($request->urgency_level === 'high')
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                                Prioridade Alta
                            </span>
                        @else
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                Normal
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4">
                <p class="text-sm text-gray-500">Nenhum pedido urgente no momento. Glória a Deus!</p>
            </div>
        @endforelse
    </div>
    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 text-center">
        <a href="{{ route('member.intercessor.requests.create') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
            + Criar Pedido de Oração
        </a>
    </div>
</div>

