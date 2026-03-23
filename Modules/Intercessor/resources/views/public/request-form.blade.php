@extends('homepage::layouts.master')

@section('title', 'Pedido de Oração')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="px-6 sm:px-10 py-8 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Envie seu Pedido de Oração</h1>
            <p class="mt-2 text-sm sm:text-base text-indigo-100">
                Sua necessidade é importante para Deus e para a igreja. Os detalhes serão tratados com sigilo e cuidado pastoral.
            </p>
        </div>

        <form action="{{ route('public.intercessor.requests.store') }}" method="POST" class="p-6 sm:p-10 space-y-6">
            @csrf

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-sm font-medium border border-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-red-50 text-red-800 text-sm font-medium border border-red-100">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 text-red-800 text-sm border border-red-100">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Nome (opcional)</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">E-mail (opcional)</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Telefone (opcional)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Categoria</label>
                    <select name="category_id" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">Selecione</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Título do Pedido</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Descrição do Pedido</label>
                <textarea name="description" rows="5" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none resize-y">{{ old('description') }}</textarea>
                <p class="mt-1 text-[11px] text-gray-500">
                    Evite expor detalhes íntimos de terceiros. Deus conhece cada situação.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Nível de Sigilo</label>
                    <select name="privacy_level" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="public" @selected(old('privacy_level') === 'public')>Público (igreja pode ver)</option>
                        <option value="members_only" @selected(old('privacy_level') === 'members_only')>Apenas membros logados</option>
                        <option value="intercessors_only" @selected(old('privacy_level') === 'intercessors_only')>Apenas equipe de intercessão</option>
                        <option value="pastoral_only" @selected(old('privacy_level') === 'pastoral_only')>Somente pastoral/liderança</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Urgência</label>
                    <select name="urgency_level" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="normal" @selected(old('urgency_level') === 'normal')>Normal</option>
                        <option value="high" @selected(old('urgency_level') === 'high')>Alta</option>
                        <option value="critical" @selected(old('urgency_level') === 'critical')>Crítica</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 rounded-xl shadow-lg shadow-indigo-500/30 transition-all">
                    <x-icon name="hands-praying" class="w-5 h-5 mr-2" />
                    Enviar Pedido
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

