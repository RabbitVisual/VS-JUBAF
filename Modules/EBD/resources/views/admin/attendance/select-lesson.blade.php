@extends('admin::components.layouts.master')

@section('title', 'Selecionar Lição | Chamada EBD')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest bg-emerald-600 text-white rounded">Fluxo de Presença</span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Escola Bíblica Dominical</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Selecionar <span class="text-emerald-600">Lição</span></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-2xl font-medium">Escolha a atividade pedagógica para a qual deseja registrar a frequência dos alunos.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ebd.attendance.index') }}"
                class="inline-flex items-center px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-sm font-bold transition-all hover:bg-gray-200 dark:hover:bg-gray-700">
                <x-icon name="arrow-left" style="duotone" class="mr-2 h-4 w-4" />
                Voltar
            </a>
        </div>
    </div>

    <!-- Lessons List -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
        @if($lessons->isEmpty())
            <div class="p-20 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-icon name="calendar-exclamation" style="duotone" class="w-10 h-10 text-gray-200" />
                </div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Nenhuma lição disponível</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-2 mb-8 max-w-sm mx-auto font-medium text-sm">É necessário criar uma lição no calendário antes de iniciar o processo de chamada.</p>
                <a href="{{ route('admin.ebd.lessons.create') }}"
                    class="inline-flex items-center px-8 py-4 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-xl shadow-emerald-600/20">
                    Criar Primeira Lição
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Atividade / Conteúdo</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Classe Destino</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Cronograma</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($lessons as $lesson)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                                        <x-icon name="book-bible" style="duotone" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $lesson->title }}</div>
                                        @if($lesson->bible_reference)
                                            <div class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">{{ $lesson->bible_reference }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                    {{ $lesson->ebdClass->name }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-tighter">{{ $lesson->lesson_date->format('d/m/Y') }}</div>
                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($lesson->lesson_time)->format('H:i') }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border
                                    @if($lesson->status === 'completed') bg-green-50 border-green-100 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400
                                    @elseif($lesson->status === 'in_progress') bg-blue-50 border-blue-100 text-blue-700 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400
                                    @else bg-gray-50 border-gray-100 text-gray-400 dark:bg-gray-800 dark:border-gray-700 @endif">
                                    {{ $lesson->status_display }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                <a href="{{ route('admin.ebd.attendance.create', ['lesson_id' => $lesson->id]) }}"
                                    class="inline-flex items-center px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20 group-hover:scale-105 active:scale-95">
                                    Registrar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

