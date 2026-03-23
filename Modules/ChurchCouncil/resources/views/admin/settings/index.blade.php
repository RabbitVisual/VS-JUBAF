@extends('admin::components.layouts.master')

@section('content')
<div class="space-y-8">
    @if(session('success'))
        <div class="rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
            <x-icon name="check-circle" class="w-5 h-5 flex-shrink-0" />
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
            <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0" />
            {{ session('error') }}
        </div>
    @endif

    <!-- Hero Header (padrão configuração) -->
    <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-gray-900 to-gray-800 text-white shadow-xl border border-gray-700/50">
        <div class="absolute inset-0 dash-pattern opacity-10"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-linear-to-l from-blue-600/20 to-transparent"></div>
        <div class="relative p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider">Conselho</span>
                    <span class="px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-bold uppercase tracking-wider">Configurações</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Configurações do Conselho</h1>
                <p class="text-gray-300 max-w-xl">Personalize parâmetros, regras de aprovação e notificações. Aplicadas em todo o sistema (admin, painel do membro, PDFs e Tesouraria).</p>
            </div>
            <div class="flex-shrink-0">
                <button type="submit" form="churchcouncil-settings-form" class="px-6 py-3 rounded-xl bg-white text-gray-900 font-bold hover:bg-gray-100 shadow-lg shadow-white/10 inline-flex items-center gap-2">
                    <x-icon name="check" class="w-5 h-5 text-blue-600" /> Salvar
                </button>
            </div>
        </div>
    </div>

    <form id="churchcouncil-settings-form" method="POST" action="{{ route('admin.churchcouncil.settings.update') }}" class="space-y-6" onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Salvando...' } }))">
        @csrf
        @method('PUT')

        <!-- General Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                    <x-icon name="cog" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Configurações Gerais</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Parâmetros básicos usados em reuniões, atas, editais e telas do conselho.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="council_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Nome do Conselho
                    </label>
                    <input type="text" name="council_name" id="council_name"
                        value="{{ old('council_name', $settings['council_name'] ?? 'Conselho da Igreja') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                        placeholder="Ex: Conselho da Igreja">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Exibido em títulos, PDFs de atas e convocações, painel do membro e sidebar. Ex.: &quot;Conselho da Igreja&quot; ou &quot;Conselho Diretor&quot;.</p>
                    @error('council_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meeting_frequency" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Frequência de Reuniões
                    </label>
                    <select name="meeting_frequency" id="meeting_frequency"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                        <option value="weekly" {{ ($settings['meeting_frequency'] ?? 'monthly') === 'weekly' ? 'selected' : '' }}>Semanal</option>
                        <option value="biweekly" {{ ($settings['meeting_frequency'] ?? 'monthly') === 'biweekly' ? 'selected' : '' }}>Quinzenal</option>
                        <option value="monthly" {{ ($settings['meeting_frequency'] ?? 'monthly') === 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="quarterly" {{ ($settings['meeting_frequency'] ?? 'monthly') === 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                    </select>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Usado em editais de convocação (PDF) e referência de periodicidade das reuniões.</p>
                </div>

                <div>
                    <label for="quorum_percentage" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Quorum Mínimo (%)
                    </label>
                    <div class="relative">
                        <input type="number" name="quorum_percentage" id="quorum_percentage" min="1" max="100"
                            value="{{ old('quorum_percentage', $settings['quorum_percentage'] ?? 50) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm pr-8">
                        <span class="absolute right-4 top-2.5 text-gray-400 font-medium">%</span>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Percentual mínimo de membros presentes para reunião ter quorum. Usado em relatórios e validações de reunião.</p>
                </div>

                <div>
                    <label for="voting_deadline_days" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Prazo para Votação (dias)
                    </label>
                    <input type="number" name="voting_deadline_days" id="voting_deadline_days" min="1" max="30"
                        value="{{ old('voting_deadline_days', $settings['voting_deadline_days'] ?? 7) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Prazo padrão em dias para encerramento de votações em pautas (1 a 30 dias).</p>
                </div>
            </div>
            </div>
        </div>

        <!-- Approval Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg text-green-600 dark:text-green-400">
                    <x-icon name="check-circle" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Regras de Aprovação</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Limites e prazos que integram o conselho com a Tesouraria e fluxos de aprovação.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="auto_approve_budget_limit" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Limite para Aprovação Automática (R$)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">R$</span>
                        </div>
                        <input type="number" name="auto_approve_budget_limit" id="auto_approve_budget_limit" min="0" step="0.01"
                            value="{{ old('auto_approve_budget_limit', $settings['auto_approve_budget_limit'] ?? 1000) }}"
                            class="w-full pl-10 px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Despesas acima deste valor na Tesouraria geram solicitação de aprovação do conselho. Abaixo do limite, a despesa segue sem exigir aprovação formal.</p>
                </div>

                <div>
                    <label for="approval_deadline_days" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Prazo Máximo para Aprovação (dias)
                    </label>
                    <input type="number" name="approval_deadline_days" id="approval_deadline_days" min="1" max="90"
                        value="{{ old('approval_deadline_days', $settings['approval_deadline_days'] ?? 15) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Prazo máximo em dias para o conselho responder a uma solicitação de aprovação (1 a 90 dias). Usado em alertas e relatórios.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    Tipos de Aprovação Habilitados
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Selecione quais tipos de aprovação o conselho utiliza. Estes tipos aparecem ao criar solicitações de aprovação (orçamento, projetos, políticas, etc.).</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $approvalTypes = [
                            'budget' => 'Orçamento',
                            'project' => 'Projeto',
                            'personnel' => 'Pessoal',
                            'policy' => 'Política',
                            'facility' => 'Instalação',
                            'other' => 'Outro'
                        ];
                        $enabledTypes = $settings['enabled_approval_types'] ?? ['budget', 'project', 'policy'];
                    @endphp
                    @foreach($approvalTypes as $key => $label)
                        <label class="relative flex items-start p-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="enabled_approval_types[]" value="{{ $key }}"
                                    {{ in_array($key, $enabledTypes) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg text-yellow-600 dark:text-yellow-400">
                    <x-icon name="bell" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Notificações</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Define como e quando os membros do conselho são notificados (e-mail e alertas na aplicação).</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-start p-4 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center h-5">
                        <input type="hidden" name="email_notifications" value="0">
                        <input type="checkbox" name="email_notifications" id="email_notifications" value="1"
                            {{ ($settings['email_notifications'] ?? true) ? 'checked' : '' }}
                            class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="ml-3">
                        <label for="email_notifications" class="font-bold text-gray-900 dark:text-white">
                            Notificações por E-mail
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Enviar notificações importantes (novas pautas, aprovações, convocações) por e-mail aos membros do conselho.</p>
                    </div>
                </div>

                <div class="flex items-start p-4 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center h-5">
                        <input type="hidden" name="reminder_notifications" value="0">
                        <input type="checkbox" name="reminder_notifications" id="reminder_notifications" value="1"
                            {{ ($settings['reminder_notifications'] ?? true) ? 'checked' : '' }}
                            class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="ml-3">
                        <label for="reminder_notifications" class="font-bold text-gray-900 dark:text-white">
                            Lembretes Automáticos
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Enviar lembretes automáticos 24h antes de reuniões agendadas.</p>
                    </div>
                </div>

                <div class="flex items-start p-4 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center h-5">
                        <input type="hidden" name="voting_reminders" value="0">
                        <input type="checkbox" name="voting_reminders" id="voting_reminders" value="1"
                            {{ ($settings['voting_reminders'] ?? true) ? 'checked' : '' }}
                            class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="ml-3">
                        <label for="voting_reminders" class="font-bold text-gray-900 dark:text-white">
                            Alertas de Votação
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Notificar membros quando houver votações pendentes ou prestes a expirar.</p>
                    </div>
                </div>

                <div class="flex items-start p-4 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center h-5">
                        <input type="hidden" name="allow_admin_approval" value="0">
                        <input type="checkbox" name="allow_admin_approval" id="allow_admin_approval" value="1"
                            {{ ($settings['allow_admin_approval'] ?? false) ? 'checked' : '' }}
                            class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="ml-3">
                        <label for="allow_admin_approval" class="font-bold text-gray-900 dark:text-white">
                            Permitir aprovação por administrador
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Se ativo, usuários com perfil de pastor ou administrador podem aprovar/rejeitar pautas e fechamentos mesmo sem serem membros do conselho. Usado no admin e na Tesouraria (fechamento mensal).</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4">
            <button type="submit"
                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-blue-500/30 flex items-center">
                <x-icon name="check" class="w-5 h-5 mr-2" />
                Salvar Configurações
            </button>
        </div>
    </form>
</div>
@endsection
