@extends('memberpanel::components.layouts.master')

@section('page-title', 'Editar Pedido de Oração')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs font-medium text-gray-400 uppercase tracking-widest">
                    <li>Painel</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li>Intercessão</li>
                    <li><x-icon name="chevron-right" style="duotone" class="w-3 h-3" /></li>
                    <li class="text-blue-600 dark:text-blue-400">Editar Pedido</li>
                </ol>
            </nav>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Editar Pedido</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Atualize as informações do seu pedido de oração.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('member.intercessor.requests.index') }}" class="px-6 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all active:scale-95">
                 Cancelar
             </a>
        </div>
    </div>

    <form action="{{ route('member.intercessor.requests.update', $request) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 pb-4 border-b border-gray-50 dark:border-gray-700">
                        <x-icon name="pencil" class="w-4 h-4" /> Detalhes do Pedido
                    </h3>

                    <div class="space-y-6">
                        <div class="space-y-3">
                            <label for="title" class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Título do Pedido</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $request->title) }}" required
                                class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-2xl text-gray-900 dark:text-white font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="Ex: Oração pela minha família">
                        </div>

                        <div class="space-y-3">
                            <label for="description" class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Descrição Detalhada</label>
                            <textarea name="description" id="description" rows="8" required
                                class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-4xl text-gray-900 dark:text-white font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none leading-relaxed" placeholder="Conte os detalhes da sua necessidade...">{{ old('description', $request->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Configuration Card -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 space-y-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 pb-4 border-b border-gray-50 dark:border-gray-700">
                        <x-icon name="cog" class="w-4 h-4" /> Configurações
                    </h3>

                    <div class="space-y-6">
                        <div class="space-y-3">
                            <label for="category_id" class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Categoria</label>
                            <select name="category_id" id="category_id" required
                                class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $request->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label for="urgency_level" class="block text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Nível de Urgência</label>
                            <select name="urgency_level" id="urgency_level" required
                                class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none">
                                <option value="normal" {{ old('urgency_level', $request->urgency_level) === 'normal' ? 'selected' : '' }}>Prioridade Normal</option>
                                <option value="high" {{ old('urgency_level', $request->urgency_level) === 'high' ? 'selected' : '' }}>Alta Prioridade</option>
                                <option value="critical" {{ old('urgency_level', $request->urgency_level) === 'critical' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700 hover:bg-white dark:hover:bg-gray-800 transition-all">
                            <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" {{ old('is_anonymous', $request->is_anonymous) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_anonymous" class="text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Pedido Anônimo</label>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs rounded-2xl transition-all shadow-xl shadow-blue-600/20 uppercase tracking-widest active:scale-95">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

