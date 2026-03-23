@extends('memberpanel::components.layouts.master')

@section('title', 'Pedidos de Oração')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-in fade-in slide-in-from-bottom-4 duration-700">

    <!-- Header & Hero -->
    <div class="mb-12 relative">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 bg-white dark:bg-gray-950 p-8 sm:p-12 rounded-[3rem] border border-gray-100 dark:border-white/5 shadow-sm">
            <div class="flex-1">
                <nav class="flex items-center gap-2 text-[10px] font-black text-rose-600 dark:text-rose-500 uppercase tracking-widest mb-4">
                    <span>Ação Social</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-800"></span>
                    <span class="text-gray-400">Pilar Espiritual</span>
                </nav>
                <h1 class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-tight mb-4 italic">
                    Podemos <span class="text-transparent bg-clip-text bg-linear-to-r from-rose-600 to-orange-500">Orar</span> <br>com você?
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg max-w-md font-medium leading-relaxed italic">
                    "Onde dois ou três estiverem reunidos em meu nome, ali estou eu no meio deles."
                </p>
            </div>
            <div class="hidden md:block">
                <div class="w-48 h-48 bg-rose-50 dark:bg-rose-900/10 rounded-full flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 blur-2xl bg-rose-500"></div>
                    <x-icon name="hands-praying" class="text-7xl text-rose-500/40 relative z-10" />
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-2xl text-green-700 dark:text-green-300 flex items-center gap-3 font-bold text-sm shadow-sm transition-all animate-bounce">
            <x-icon name="circle-check" class="text-lg" />
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 pb-20">
        {{-- Form: Novo Pedido --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-950 p-8 rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-2xl sticky top-8">
                <div class="flex items-center gap-3 mb-8">
                    <span class="w-10 h-10 rounded-xl bg-linear-to-br from-rose-500 to-orange-400 text-white flex items-center justify-center shadow-lg">
                        <x-icon name="feather" />
                    </span>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight italic">Novo Pedido</h2>
                </div>

                <form action="{{ route('socialaction.member.prayer.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Para quem é a oração?</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', auth()->user()->name) }}"
                               class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-rose-500 transition-all text-sm font-medium"
                               placeholder="Ex: Por mim, pela minha mãe...">
                    </div>

                    <div>
                        <label for="request" class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Descreva seu pedido</label>
                        <textarea name="request" id="request" rows="5" required
                                  class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border-none rounded-2xl focus:ring-2 focus:ring-rose-500 transition-all text-sm font-medium resize-none italic"
                                  placeholder="Compartilhe seu coração conosco..."></textarea>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic leading-tight mb-1">Privacidade</p>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 italic">Deseja manter anonimato?</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_anonymous" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black text-xs uppercase tracking-[0.25em] shadow-xl hover:bg-rose-600 hover:text-white transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Enviar Pedido de Oração
                        </button>
                    </div>

                    <p class="text-[10px] text-gray-400 dark:text-gray-600 text-center italic leading-relaxed px-4">
                        Seu pedido será enviado aos líderes de intercessão da Ação Social. Seus dados são protegidos por criptografia (LGPD).
                    </p>
                </form>
            </div>
        </div>

        {{-- List: Meus Pedidos --}}
        <div class="lg:col-span-3">
            <div class="mb-8 flex items-end justify-between px-2">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight italic">Meus <span class="text-rose-600">Pedidos</span></h2>
                    <p class="text-gray-500 font-medium italic">Acompanhe seus clamores ao Senhor.</p>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    {{ count($myRequests) }} Registros
                </div>
            </div>

            <div class="space-y-6">
                @forelse($myRequests as $prayer)
                    <div class="group bg-white dark:bg-gray-950 p-8 rounded-[2.5rem] border border-gray-100 dark:border-white/5 hover:border-rose-500/20 hover:shadow-2xl transition-all duration-500 relative overflow-hidden">
                        {{-- Status Badge absolute --}}
                        <div class="absolute top-8 right-8">
                             <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{
                                match($prayer->status) {
                                    'prayed' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                    'archived' => 'bg-gray-100 text-gray-500',
                                    default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 animate-pulse',
                                }
                            }}">
                                {{ $prayer->status_label }}
                            </span>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-white/5 flex items-center justify-center text-rose-500 shrink-0 group-hover:bg-rose-500 group-hover:text-white transition-all duration-500 shadow-sm">
                                <x-icon :name="$prayer->status === 'prayed' ? 'hands-holding-heart' : 'hands-praying'" class="text-2xl" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="mb-4">
                                    <h4 class="text-lg font-black text-gray-900 dark:text-white mb-1 italic">
                                        {{ $prayer->name }}
                                        @if($prayer->is_anonymous)
                                            <span class="ml-2 text-[9px] font-bold text-gray-400 uppercase tracking-widest">(Anônimo)</span>
                                        @endif
                                    </h4>
                                    <p class="text-[10px] font-black text-rose-600 uppercase tracking-[0.15em]">{{ $prayer->created_at->format('d/m/Y - H:i') }}</p>
                                </div>
                                <div class="bg-gray-50/50 dark:bg-white/5 p-5 rounded-2xl border border-gray-100 dark:border-white/10">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium italic leading-relaxed">
                                        "{{ $prayer->request }}"
                                    </p>
                                </div>

                                @if($prayer->status === 'prayed' && $prayer->prayed_at)
                                    <div class="mt-6 flex items-center gap-2 text-[10px] font-black text-green-600 dark:text-green-400 uppercase tracking-widest">
                                        <x-icon name="check-double" class="scale-110" /> Este pedido já foi levado em oração pela intercessão.
                                    </div>
                                @endif

                                @if($prayer->status === 'pending')
                                    <div class="mt-6 flex items-center gap-2 text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest">
                                        <x-icon name="spinner" class="animate-spin" /> Em intercessão contínua diante do altar.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-24 text-center bg-gray-50/50 dark:bg-gray-900/10 rounded-[3rem] border border-dashed border-gray-200 dark:border-white/5">
                        <x-icon name="heart-crack" class="text-4xl text-gray-300 mb-4 block" />
                        <p class="text-gray-500 font-bold italic">Você ainda não enviou pedidos de oração pelo portal.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
