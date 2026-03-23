@extends('memberpanel::components.layouts.master')

@section('page-title', 'Mural de Intercessão')
@php use Illuminate\Support\Facades\Storage; @endphp


@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12" x-data="prayerRoom">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs font-medium text-gray-400 uppercase tracking-widest">
                    <li>Painel</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li class="text-purple-600 dark:text-purple-400">Mural de Intercessão</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Mural de Intercessão</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">"Levai as cargas uns dos outros, e assim cumprireis a lei de Cristo." - Gálatas 6:2</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-2">
                 <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                 <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">Comunidade Ativa</span>
             </div>
             @if(\Modules\Intercessor\App\Services\IntercessorSettings::get('allow_requests'))
                 <a href="{{ route('member.intercessor.requests.create') }}" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-0.5" data-tour="intercessor-new-request">
                     Novo Pedido
                 </a>
             @endif
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1 space-y-6" data-tour="intercessor-filters">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 p-8 sticky top-6">
                <h3 class="text-sm font-black text-gray-400 dark:text-gray-500 mb-8 uppercase tracking-widest flex items-center gap-2">
                    <x-icon name="filter" style="duotone" class="w-4 h-4" /> Filtros
                </h3>

                <nav class="space-y-3">
                    <a href="{{ route('member.intercessor.room.index') }}"
                       class="flex items-center justify-between px-4 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all {{ !request('urgency') && !request('filter') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-slate-800' }}">
                        <span>Recentes</span>
                        @if(!request('urgency') && !request('filter')) <x-icon name="check" style="duotone" class="w-3 h-3" /> @endif
                    </a>

                    <a href="{{ route('member.intercessor.room.index', ['urgency' => 'high']) }}"
                       class="flex items-center justify-between px-4 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all {{ request('urgency') === 'high' ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-slate-800' }}">
                        <span>Alta Prioridade</span>
                        @if(request('urgency') === 'high') <x-icon name="check" style="duotone" class="w-3 h-3" /> @endif
                    </a>

                    <a href="{{ route('member.intercessor.room.index', ['filter' => 'new']) }}"
                       class="flex items-center justify-between px-4 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all {{ request('filter') === 'new' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-slate-800' }}">
                        <span>Novos Pedidos</span>
                        @if(request('filter') === 'new') <x-icon name="check" style="duotone" class="w-3 h-3" /> @endif
                    </a>
                </nav>
            </div>
        </div>

        <!-- Feed -->
        <div class="lg:col-span-3" data-tour="intercessor-requests">
             @if ($feed->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($feed as $request)
                        <div class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-l-8 border-gray-100 dark:border-slate-800 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col h-full relative"
                             style="border-left-color: {{ $request->urgency_level === 'critical' ? '#EF4444' : ($request->urgency_level === 'high' ? '#F59E0B' : '#10B981') }};">

                            <!-- Badges -->
                            <div class="flex items-center justify-between mb-6">
                               <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase tracking-widest bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-gray-400 border border-gray-200/50 dark:border-slate-700">
                                        {{ $request->category->name }}
                                    </span>
                                    @if($request->urgency_level === 'critical')
                                        <span class="px-3 py-1 text-[9px] font-black rounded-full uppercase tracking-widest bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 animate-pulse border border-red-200/50">
                                            Urgente
                                        </span>
                                    @endif
                               </div>
                               <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $request->created_at->diffForHumans() }}</span>
                            </div>

                            <!-- Title & Subtitle -->
                            <div class="mb-6">
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 mb-3 leading-tight tracking-tight">
                                    <a href="{{ route('member.intercessor.room.show', $request) }}">
                                        {{ $request->title }}
                                    </a>
                                </h3>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed mb-4">
                                    {{ $request->description }}
                                </p>
                            </div>

                            <!-- Footer -->
                            <div class="mt-auto pt-6 border-t border-gray-50 dark:border-slate-800 flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                     <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 flex items-center justify-center text-gray-400 dark:text-gray-500 text-[10px] overflow-hidden shadow-inner font-black">
                                        @if(!$request->is_anonymous && $request->user && $request->user->photo)
                                            <img src="{{ Storage::url($request->user->photo) }}" alt="{{ $request->user->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr($request->is_anonymous ? 'A' : ($request->user->name ?? 'A'), 0, 1) }}
                                        @endif
                                     </div>
                                     {{ $request->is_anonymous ? 'Anônimo' : Str::limit($request->user->name ?? 'Anônimo', 15) }}
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="flex items-center gap-1.5 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                         <x-icon name="users" style="duotone" class="w-3.5 h-3.5" />
                                         {{ $request->commitments->count() }}
                                    </span>

                                    @if(Auth::id() !== $request->user_id && !$request->commitments->where('user_id', Auth::id())->count())
                                    <form action="{{ route('member.intercessor.room.commit', $request) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100/50 dark:border-blue-900/30 transition-all">
                                            Vou Orar
                                        </button>
                                    </form>
                                    @elseif($request->commitments->where('user_id', Auth::id())->count())
                                            <x-icon name="check" style="duotone" class="w-3 h-3" /> Orando
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $feed->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <x-icon name="search" style="duotone" class="w-10 h-10 text-gray-400" />
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum pedido encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                        O mural está vazio no momento.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('prayerRoom', () => ({
            init() {
                if (window.Echo) {
                    window.Echo.channel('intercessor-global')
                        .listen('.Modules\\Intercessor\\App\\Events\\PrayerCommitmentCreated', (e) => {
                             console.log('New commitment:', e);
                             // Reload or notify (simplified)
                        });
                }
            }
        }))
    })
</script>
@endpush
@endsection
