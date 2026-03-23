@extends('memberpanel::components.layouts.master')

@section('page-title', 'Meus Pedidos de Oração')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs font-medium text-gray-400 uppercase tracking-widest">
                    <li>Painel</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li>Intercessão</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li class="text-blue-600 dark:text-blue-400">Meus Pedidos</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Meus Pedidos</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Gerencie e acompanhe seus pedidos de oração.</p>
        </div>
        <div class="flex items-center gap-3" data-tour="intercessor-request-create-link">
             <div class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl flex items-center gap-2">
                 <span class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></span>
                 <span class="text-xs font-black text-blue-700 dark:text-blue-400 uppercase tracking-widest">{{ $myRequests->count() }} Pedidos</span>
             </div>
             @if(\Modules\Intercessor\App\Services\IntercessorSettings::get('allow_requests'))
                <a href="{{ route('member.intercessor.requests.create') }}" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-0.5">
                    Novo Pedido
                </a>
            @endif
        </div>
    </div>

    @if($myRequests->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm col-span-full">
            <div class="mx-auto w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-6">
                <x-icon name="comment" class="w-12 h-12 text-blue-500" />
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Compartilhe sua necessidade</h3>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed mb-8">
                Você ainda não criou nenhum pedido. A comunidade está pronta para orar por você.
            </p>
             @if(\Modules\Intercessor\App\Services\IntercessorSettings::get('allow_requests'))
                <a href="{{ route('member.intercessor.requests.create') }}"
                    class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all hover:-translate-y-0.5">
                    <x-icon name="plus" class="w-5 h-5 mr-2" />
                    Criar Primeiro Pedido
                </a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-tour="intercessor-requests">
            @foreach($myRequests as $request)
                <div class="group bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all flex flex-col overflow-hidden">
                    <div class="p-8 flex-1">
                        <!-- Badges -->
                        <div class="flex items-center justify-between mb-6">
                            <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase tracking-widest
                                {{ $request->status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-100/50' : '' }}
                                {{ $request->status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-100/50' : '' }}
                                {{ $request->status === 'answered' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-100/50' : 'bg-gray-50 text-gray-700 border border-gray-100' }}">
                                {{ $request->status === 'active' ? 'Ativo' : ($request->status === 'pending' ? 'Pendente' : ($request->status === 'answered' ? 'Concluído' : $request->status)) }}
                            </span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $request->created_at->format('d/m/Y') }}</span>
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-3 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $request->title }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed font-medium">
                                {{ $request->description }}
                            </p>
                        </div>

                        <!-- Stats -->
                        <div class="flex items-center gap-6 pt-6 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="flex items-center gap-1.5 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <x-icon name="users" class="w-4 h-4 text-gray-300" />
                                {{ $request->commitments_count }} Orando
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <x-icon name="chat" class="w-4 h-4 text-gray-300" />
                                {{ $request->interactions_count }} Mensagens
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-6 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700 mt-auto">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('member.intercessor.room.show', $request) }}" class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-[10px] font-black shadow-sm hover:bg-gray-50 transition-all text-center uppercase tracking-widest">
                                Visualizar
                            </a>

                            @if($request->status === 'active')
                                <a href="{{ route('member.intercessor.requests.testimony', $request) }}" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-all text-center uppercase tracking-widest">
                                    Concluir
                                </a>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('member.intercessor.requests.edit', $request) }}" class="p-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-blue-600 rounded-xl shadow-sm transition-all">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </a>
                                @if(auth()->user()->hasRole('admin'))
                                    <form action="{{ route('member.intercessor.requests.destroy', $request) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-red-600 rounded-xl shadow-sm transition-all">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $myRequests->links() }}
        </div>
    @endif
</div>
@endsection

