@extends('memberpanel::components.layouts.master')

@section('page-title', isset($report) ? 'Editar relatório' : 'Novo relatório – ' . $ministry->name)

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-2xl mx-auto px-6 pt-8 space-y-8">
            @if(session('error'))
                <div class="rounded-2xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20 px-4 py-3 text-sm font-medium text-rose-800 dark:text-rose-200 flex items-center gap-2">
                    <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" /> {{ session('error') }}
                </div>
            @endif
            <nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-slate-400 font-medium">
                <a href="{{ route('memberpanel.ministries.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 uppercase tracking-widest text-[10px] font-black">Ministérios</a>
                <x-icon name="chevron-right" class="w-3 h-3 opacity-50" />
                <a href="{{ route('memberpanel.ministries.show', $ministry) }}" class="hover:text-blue-600 dark:hover:text-blue-400 font-bold">{{ $ministry->name }}</a>
                <x-icon name="chevron-right" class="w-3 h-3 opacity-50" />
                <span class="text-gray-900 dark:text-white font-bold">{{ isset($report) ? 'Editar relatório' : 'Novo relatório' }}</span>
            </nav>

            <div
                x-data="{
                    step: 1,
                    totalSteps: 3,
                    submitting: false,
                    qualitative_summary: @js(old('qualitative_summary', $report->qualitative_summary ?? '')),
                    highlights: @js(old('highlights', $report->highlights ?? '')),
                    challenges: @js(old('challenges', $report->challenges ?? '')),
                    prayer_requests: @js(old('prayer_requests', $report->prayer_requests ?? '')),
                }"
                class="bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-icon name="clipboard-document-list" class="w-6 h-6" />
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ isset($report) ? 'Editar relatório' : 'Relatório mensal' }}</h1>
                        <p class="text-sm text-gray-500 dark:text-slate-400">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F/Y') }}</p>
                    </div>
                </div>

                <!-- Stepper -->
                <div class="mb-8">
                    <ol class="flex items-center justify-between text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">
                        <li class="flex-1 flex items-center gap-2">
                            <div class="flex items-center justify-center w-7 h-7 rounded-full border"
                                 :class="step >= 1 ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 dark:border-slate-600'">
                                1
                            </div>
                            <span class="hidden sm:inline">Conteúdo</span>
                        </li>
                        <li class="flex-1 flex items-center gap-2 justify-center">
                            <div class="h-px flex-1 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700"></div>
                            <div class="flex items-center justify-center w-7 h-7 rounded-full border"
                                 :class="step >= 2 ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 dark:border-slate-600'">
                                2
                            </div>
                            <span class="hidden sm:inline">Destaques</span>
                        </li>
                        <li class="flex-1 flex items-center gap-2 justify-end">
                            <div class="h-px flex-1 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-slate-700 dark:via-slate-600 dark:to-slate-700"></div>
                            <div class="flex items-center justify-center w-7 h-7 rounded-full border"
                                 :class="step === 3 ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 dark:border-slate-600'">
                                3
                            </div>
                            <span class="hidden sm:inline">Desafios &amp; Oração</span>
                        </li>
                    </ol>
                </div>

                <form
                    action="{{ isset($report) ? route('memberpanel.ministries.reports.update', [$ministry, $report]) : route('memberpanel.ministries.reports.store', $ministry) }}"
                    method="POST"
                    class="space-y-8"
                    @submit="submitting = true">
                    @csrf
                    @if(isset($report)) @method('PUT') @endif
                    @if(!isset($report))
                        <input type="hidden" name="report_year" value="{{ $year }}">
                        <input type="hidden" name="report_month" value="{{ $month }}">
                    @endif

                    <!-- Step 1: Conteúdo -->
                    <div x-show="step === 1" x-transition>
                        <div>
                            <label for="qualitative_summary" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Resumo qualitativo</label>
                            <textarea
                                name="qualitative_summary"
                                id="qualitative_summary"
                                rows="6"
                                x-model="qualitative_summary"
                                class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                placeholder="Descreva as principais atividades realizadas, resultados e percepções espirituais do mês.">{{ old('qualitative_summary', $report->qualitative_summary ?? '') }}</textarea>
                            <div class="mt-1 flex items-center justify-between text-[11px] text-gray-400 dark:text-slate-500">
                                <span>Recomendado pelo menos 200 caracteres.</span>
                                <span><x-icon name="keyboard" class="w-3 h-3 inline mr-1" /> <span x-text="qualitative_summary.length"></span> caracteres</span>
                            </div>
                            @error('qualitative_summary')<p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- Step 2: Destaques -->
                    <div x-show="step === 2" x-transition>
                        <div class="space-y-4">
                            <div>
                                <label for="highlights" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Destaques do mês</label>
                                <textarea
                                    name="highlights"
                                    id="highlights"
                                    rows="3"
                                    x-model="highlights"
                                    class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                    placeholder="Liste as principais conquistas, testemunhos e avanços do ministério...">{{ old('highlights', $report->highlights ?? '') }}</textarea>
                                <div class="mt-1 flex items-center justify-end text-[11px] text-gray-400 dark:text-slate-500">
                                    <span><x-icon name="keyboard" class="w-3 h-3 inline mr-1" /> <span x-text="highlights.length"></span> caracteres</span>
                                </div>
                            </div>
                            <div>
                                <label for="challenges" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Desafios e necessidades</label>
                                <textarea
                                    name="challenges"
                                    id="challenges"
                                    rows="3"
                                    x-model="challenges"
                                    class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                    placeholder="Aponte dificuldades encontradas, limitações de equipe, recursos ou processos...">{{ old('challenges', $report->challenges ?? '') }}</textarea>
                                <div class="mt-1 flex items-center justify-end text-[11px] text-gray-400 dark:text-slate-500">
                                    <span><x-icon name="keyboard" class="w-3 h-3 inline mr-1" /> <span x-text="challenges.length"></span> caracteres</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Desafios & Oração -->
                    <div x-show="step === 3" x-transition>
                        <div class="space-y-4">
                            <div>
                                <label for="prayer_requests" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Pedidos de oração</label>
                                <textarea
                                    name="prayer_requests"
                                    id="prayer_requests"
                                    rows="3"
                                    x-model="prayer_requests"
                                    class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                    placeholder="Compartilhe motivos de oração específicos pelo ministério, equipe e frutos.">{{ old('prayer_requests', $report->prayer_requests ?? '') }}</textarea>
                                <div class="mt-1 flex items-center justify-end text-[11px] text-gray-400 dark:text-slate-500">
                                    <span><x-icon name="hands-praying" class="w-3 h-3 inline mr-1" /> <span x-text="prayer_requests.length"></span> caracteres</span>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-blue-100 dark:border-blue-900/40 bg-blue-50/60 dark:bg-blue-900/10 px-4 py-3 text-xs text-blue-800 dark:text-blue-200 flex items-start gap-2">
                                <x-icon name="circle-info" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                <p>Ao enviar o relatório, o conselho e a tesouraria poderão enxergar melhor a realidade deste ministério e apoiar com decisões, recursos e intercessão.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-slate-800 mt-4">
                        <div class="flex items-center gap-2">
                            <button type="submit" name="submit" value="0"
                                class="px-5 py-2.5 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-slate-700 transition-all"
                                :disabled="submitting">
                                Salvar rascunho
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200"
                                x-show="step > 1"
                                @click="step = Math.max(1, step - 1)">
                                <x-icon name="arrow-left" class="w-3.5 h-3.5 inline mr-1" /> Voltar
                            </button>
                            <button type="button"
                                class="px-5 py-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-all flex items-center gap-2"
                                x-show="step < totalSteps"
                                @click="step = Math.min(totalSteps, step + 1)">
                                Próximo passo <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                            </button>
                            <button type="submit" name="submit" value="1"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-lg flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                                x-show="step === totalSteps"
                                :disabled="submitting">
                                <x-icon name="paper-plane" class="w-4 h-4" />
                                <span x-text="submitting ? 'Enviando...' : 'Enviar relatório'"></span>
                            </button>
                            <a href="{{ route('memberpanel.ministries.show', $ministry) }}" class="px-4 py-2 text-gray-400 dark:text-slate-500 rounded-xl font-medium text-xs hover:underline">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
