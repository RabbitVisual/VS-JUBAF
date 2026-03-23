@extends('admin::components.layouts.master')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Novo Documento</h1>
            <a href="{{ route('admin.churchcouncil.documents.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold text-sm flex items-center">
                <x-icon name="arrow-left" class="w-4 h-4 mr-1" /> Voltar
            </a>
        </div>

        <form action="{{ route('admin.churchcouncil.documents.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título do Documento *</label>
                    <input type="text" name="title" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Estatuto Social 2024">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tipo *</label>
                    <select name="document_type" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                        <option value="minute">{{ __('churchcouncil::messages.doc_type_minute') }}</option>
                        <option value="statute">{{ __('churchcouncil::messages.doc_type_statute') }}</option>
                        <option value="regiment">{{ __('churchcouncil::messages.doc_type_regiment') }}</option>
                        <option value="regimento_interno">{{ __('churchcouncil::messages.doc_type_regimento_interno') }}</option>
                        <option value="resolution">{{ __('churchcouncil::messages.doc_type_resolution') }}</option>
                        <option value="declaracao_doutrinaria">{{ __('churchcouncil::messages.doc_type_declaracao_doutrinaria') }}</option>
                        <option value="pacto_igrejas">{{ __('churchcouncil::messages.doc_type_pacto_igrejas') }}</option>
                        <option value="other">{{ __('churchcouncil::messages.doc_type_other') }}</option>
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data do Documento *</label>
                    <input type="date" name="document_date" required value="{{ date('Y-m-d') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Description -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descrição (Opcional)</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <!-- Related Meeting -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Relacionado a Reunião (Opcional)</label>
                    <select name="meeting_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione uma reunião...</option>
                        @foreach($meetings as $meeting)
                            <option value="{{ $meeting->id }}">{{ $meeting->scheduled_date->format('d/m/Y') }} - {{ $meeting->title }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Útil para anexar a ata assinada a uma reunião específica.</p>
                </div>

                <!-- File Upload -->
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Arquivo * (PDF, Word, Excel)</label>
                    <input type="file" name="file" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <!-- Visibility -->
                <div class="col-span-2">
                    <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                        <input type="checkbox" name="is_public" value="1" id="is_public" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                        <label for="is_public" class="text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                            Tornar este documento público para todos os membros?
                            <p class="text-xs font-normal text-gray-500 mt-0.5">Se marcado, qualquer membro da igreja poderá visualizar este documento no painel.</p>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg transition-colors flex items-center gap-2">
                    <x-icon name="check" class="w-5 h-5" />
                    Enviar Documento
                </button>
            </div>
        </form>
    </div>
@endsection

