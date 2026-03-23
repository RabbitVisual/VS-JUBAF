@extends('admin::components.layouts.master')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Editar Documento</h1>
            <a href="{{ route('admin.Diretoria.documents.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold text-sm flex items-center">
                <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Voltar
            </a>
        </div>

        <form action="{{ route('admin.Diretoria.documents.update', $document) }}" method="POST"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título do Documento
                        *</label>
                    <input type="text" name="title" required value="{{ old('title', $document->title) }}"
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tipo *</label>
                    <select name="document_type" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                        <option value="minute" {{ $document->document_type == 'minute' ? 'selected' : '' }}>
                            {{ __('diretoria::messages.doc_type_minute') }}</option>
                        <option value="statute" {{ $document->document_type == 'statute' ? 'selected' : '' }}>
                            {{ __('diretoria::messages.doc_type_statute') }}</option>
                        <option value="regiment" {{ $document->document_type == 'regiment' ? 'selected' : '' }}>
                            {{ __('diretoria::messages.doc_type_regiment') }}</option>
                        <option value="regimento_interno"
                            {{ $document->document_type == 'regimento_interno' ? 'selected' : '' }}>
                            {{ __('diretoria::messages.doc_type_regimento_interno') }}</option>
                        <option value="resolution" {{ $document->document_type == 'resolution' ? 'selected' : '' }}>
                            {{ __('diretoria::messages.doc_type_resolution') }}</option>
                        <option value="declaracao_doutrinaria"
                            {{ $document->document_type == 'declaracao_doutrinaria' ? 'selected' : '' }}>
                            {{ __('diretoria::messages.doc_type_declaracao_doutrinaria') }}</option>
                        <option value="pacto_igrejas" {{ $document->document_type == 'pacto_igrejas' ? 'selected' : '' }}>
                            {{ __('diretoria::messages.doc_type_pacto_igrejas') }}</option>
                        <option value="other" {{ $document->document_type == 'other' ? 'selected' : '' }}>
                            {{ __('diretoria::messages.doc_type_other') }}</option>
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data do Documento *</label>
                    <input type="date" name="document_date" required
                        value="{{ old('document_date', $document->document_date->format('Y-m-d')) }}"
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição
                        (Opcional)</label>
                    <textarea name="description" rows="3"
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $document->description) }}</textarea>
                </div>

                <!-- Related Meeting -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Relacionado a Reunião
                        (Opcional)</label>
                    <select name="meeting_id"
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Nenhuma</option>
                        @foreach ($meetings as $meeting)
                            <option value="{{ $meeting->id }}"
                                {{ $document->meeting_id == $meeting->id ? 'selected' : '' }}>
                                {{ $meeting->scheduled_date->format('d/m/Y') }} - {{ $meeting->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Visibility -->
                <div class="col-span-2">
                    <div
                        class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                        <input type="checkbox" name="is_public" value="1" id="is_public"
                            {{ $document->is_public ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                        <label for="is_public"
                            class="text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                            Documento Público
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg transition-colors flex items-center gap-2">
                    <x-icon name="check" class="w-5 h-5" />
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
@endsection
