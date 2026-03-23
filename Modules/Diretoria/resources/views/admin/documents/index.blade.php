@extends('admin::components.layouts.master')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div
                class="rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                <x-icon name="check-circle" style="duotone" class="w-5 h-5 shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                <x-icon name="x-circle" style="duotone" class="w-5 h-5 shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        <!-- Breadcrumb + Header -->
        <div class="flex flex-col gap-4">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('admin.Diretoria.index') }}"
                    class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors flex items-center gap-1">
                    <x-icon name="users-rectangle" style="duotone" class="w-4 h-4" />
                    {{ __('Diretoria::messages.diretoria_title') }}
                </a>
                <x-icon name="chevron-right" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                <span class="text-gray-900 dark:text-white font-bold flex items-center gap-1.5">
                    <x-icon name="file-lines" style="duotone" class="w-4 h-4 text-blue-500" />
                    {{ __('Diretoria::messages.documents') }}
                </span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Documentos da Diretoria
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Estatutos, regimentos, atas e resoluções
                        oficiais.</p>
                </div>
                <a href="{{ route('admin.Diretoria.documents.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30">
                    <x-icon name="plus" style="duotone" class="w-5 h-5 shrink-0" />
                    Novo Documento
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <form action="{{ route('admin.Diretoria.documents.index') }}" method="GET"
                class="flex flex-col md:flex-row md:items-end gap-4">
                <div class="flex-1 md:max-w-xs">
                    <label
                        class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Tipo
                        de Documento</label>
                    <select name="type" onchange="this.form.submit()"
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl py-2.5 px-4 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos os tipos</option>
                        <option value="statute" {{ request('type') == 'statute' ? 'selected' : '' }}>
                            {{ __('Diretoria::messages.doc_type_statute') }}</option>
                        <option value="regiment" {{ request('type') == 'regiment' ? 'selected' : '' }}>
                            {{ __('Diretoria::messages.doc_type_regiment') }}</option>
                        <option value="regimento_interno" {{ request('type') == 'regimento_interno' ? 'selected' : '' }}>
                            {{ __('Diretoria::messages.doc_type_regimento_interno') }}</option>
                        <option value="minute" {{ request('type') == 'minute' ? 'selected' : '' }}>
                            {{ __('Diretoria::messages.doc_type_minute') }}</option>
                        <option value="resolution" {{ request('type') == 'resolution' ? 'selected' : '' }}>
                            {{ __('Diretoria::messages.doc_type_resolution') }}</option>
                        <option value="declaracao_doutrinaria"
                            {{ request('type') == 'declaracao_doutrinaria' ? 'selected' : '' }}>
                            {{ __('Diretoria::messages.doc_type_declaracao_doutrinaria') }}</option>
                        <option value="pacto_igrejas" {{ request('type') == 'pacto_igrejas' ? 'selected' : '' }}>
                            {{ __('Diretoria::messages.doc_type_pacto_igrejas') }}</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>
                            {{ __('Diretoria::messages.doc_type_other') }}</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.Diretoria.documents.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                        <x-icon name="arrow-rotate-left" style="duotone" class="w-4 h-4" />
                        {{ __('Diretoria::messages.clear_filters') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Documents List -->
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            @if ($documents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Documento</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tipo</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Data</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Enviado por</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($documents as $doc)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600 dark:text-blue-400 shrink-0">
                                                <x-icon name="file-lines" style="duotone" class="w-6 h-6" />
                                            </div>
                                            <div class="min-w-0">
                                                <span
                                                    class="block font-bold text-gray-900 dark:text-white">{{ $doc->title }}</span>
                                                @if ($doc->description)
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">{{ $doc->description }}</span>
                                                @endif
                                                @if ($doc->is_public)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 mt-1">
                                                        <x-icon name="globe" style="duotone" class="w-3 h-3" />
                                                        Público
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 capitalize">
                                            {{ \Modules\Diretoria\App\Models\AtaDocumento::getDocumentTypeLabel($doc->document_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                            <x-icon name="calendar" style="duotone" class="w-4 h-4 text-gray-400" />
                                            {{ $doc->document_date->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if ($doc->uploader && $doc->uploader->photo)
                                                <img src="{{ asset('storage/' . $doc->uploader->photo) }}" alt=""
                                                    class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700">
                                            @else
                                                <div
                                                    class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-300 ring-2 ring-gray-100 dark:ring-gray-700">
                                                    {{ strtoupper(substr($doc->uploader->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                            <span
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $doc->uploader->name ?? 'Usuário' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.Diretoria.documents.download', $doc) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                                title="{{ __('Diretoria::messages.download') }}">
                                                <x-icon name="download" style="duotone" class="w-5 h-5" />
                                            </a>
                                            <a href="{{ route('admin.Diretoria.documents.edit', $doc) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors"
                                                title="{{ __('Diretoria::messages.edit') }}">
                                                <x-icon name="pencil" style="duotone" class="w-5 h-5" />
                                            </a>
                                            <form action="{{ route('admin.Diretoria.documents.destroy', $doc) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('Tem certeza que deseja excluir este documento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                    title="Excluir">
                                                    <x-icon name="trash" style="duotone" class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($documents->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $documents->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 md:p-16 text-center">
                    <div
                        class="w-20 h-20 rounded-3xl bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mx-auto mb-5">
                        <x-icon name="file-lines" style="duotone" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Nenhum documento encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">Envie estatutos, atas de reunião ou
                        resoluções para centralizar a documentação da diretoria.</p>
                    <a href="{{ route('admin.Diretoria.documents.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30">
                        <x-icon name="plus" style="duotone" class="w-5 h-5 shrink-0" />
                        Enviar Documento
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
