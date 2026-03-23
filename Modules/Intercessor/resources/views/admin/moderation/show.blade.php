@extends('admin::components.layouts.master')

@section('title', 'Detalhes do Pedido - Moderação')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Pedido de {{ $request->user->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">Analise os detalhes e as interações deste pedido de oração.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.intercessor.moderation.index') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all duration-200">
                <x-icon name="arrow-left" class="w-5 h-5 mr-2" />
                <span>Voltar</span>
            </a>
            @if($request->status === 'pending')
            <form action="{{ route('admin.intercessor.moderation.approve', $request) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-linear-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    <x-icon name="check" class="w-5 h-5 mr-2" />
                    Aprovar Agora
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Content Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold border border-blue-100 dark:border-blue-800/50">
                            {{ substr($request->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">{{ $request->user->name }}</h3>
                            <p class="text-xs text-gray-500 font-medium">{{ $request->created_at->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                        {{ $request->category->name }}
                    </span>
                </div>

                <div class="prose dark:prose-invert max-w-none text-xl leading-relaxed text-gray-800 dark:text-gray-200 italic font-medium">
                    {!! \Modules\Intercessor\App\Services\BibleParser::parse($request->description) !!}
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Interações e Comentários</h3>
                    <span class="text-xs font-medium text-gray-500">{{ $comments->total() }} totais</span>
                </div>

                <div class="p-6">
                    @if($comments->isEmpty())
                        <div class="text-center py-10">
                            <x-icon name="chat" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhum comentário ainda.</p>
                        </div>
                    @else
                        <ul class="space-y-6">
                            @foreach($comments as $comment)
                            <li class="flex space-x-4">
                                <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 font-bold">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $comment->user->name }} @if($comment->user->id === $request->user_id) <span class="text-[10px] bg-blue-100 text-blue-600 px-1.5 rounded ml-1">Autor</span> @endif</h4>
                                        <div class="flex items-center space-x-3">
                                            <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                            <form action="{{ route('admin.intercessor.moderation.comment.destroy', [$request, $comment]) }}" method="POST" onsubmit="return confirm('Excluir este comentário?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 transition-colors">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                                        {!! \Modules\Intercessor\App\Services\BibleParser::parse($comment->content) !!}
                                    </div>

                                    <!-- Replies Loop if exists -->
                                    @if($comment->replies && $comment->replies->count() > 0)
                                        <div class="mt-4 ml-6 space-y-4 border-l-2 border-gray-100 dark:border-gray-800 pl-4">
                                            @foreach($comment->replies as $reply)
                                                <div class="flex space-x-3">
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 text-xs font-bold">
                                                        {{ substr($reply->user->name, 0, 1) }}
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <h5 class="text-xs font-bold text-gray-900 dark:text-white">{{ $reply->user->name }}</h5>
                                                            <form action="{{ route('admin.intercessor.moderation.comment.destroy', [$request, $reply]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-300 hover:text-red-500"><x-icon name="trash" class="w-3.5 h-3.5" /></button>
                                                            </form>
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                                            {!! \Modules\Intercessor\App\Services\BibleParser::parse($reply->content) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <div class="mt-8">
                            {{ $comments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Metadata -->
        <div class="space-y-6">
            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-6 pb-2 border-b border-gray-100 dark:border-gray-700">Detalhes Técnicos</h3>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status do Pedido</dt>
                        <dd>
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30' : ($request->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }} uppercase tracking-widest">
                                {{ $request->status === 'pending' ? 'Pendente' : ($request->status === 'approved' ? 'Aprovado' : 'Rejeitado') }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Urgência</dt>
                        <dd class="text-sm font-bold {{ $request->urgency_level === 'critical' ? 'text-red-600' : ($request->urgency_level === 'high' ? 'text-orange-600' : 'text-green-600') }} uppercase">
                             {{ $request->urgency_level === 'critical' ? 'Crítica' : ($request->urgency_level === 'high' ? 'Alta' : 'Normal') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Privacidade</dt>
                        <dd class="text-sm font-bold text-gray-700 dark:text-gray-300 capitalize">
                            {{ str_replace('_', ' ', $request->privacy_level) }}
                        </dd>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Interações</dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-white tracking-tighter">{{ $request->interactions()->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Orações</dt>
                            <dd class="text-2xl font-bold text-blue-600 dark:text-blue-400 tracking-tighter">{{ $request->commitments()->count() }}</dd>
                        </div>
                    </div>
                </dl>
            </div>

            <!-- Danger Zone if applicable -->
            <div class="bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-100 dark:border-red-900/30 p-6">
                <h4 class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider mb-2">Ações Sensíveis</h4>
                <form action="{{ route('admin.intercessor.moderation.destroy', $request) }}" method="POST" onsubmit="return confirm('Deseja realmente apagar este pedido e todas as suas interações?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2 bg-white dark:bg-gray-800 text-red-600 border border-red-200 dark:border-red-900/50 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white transition-all uppercase tracking-widest">
                        Excluir Pedido
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

