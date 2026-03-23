@extends('memberpanel::components.layouts.master')

@section('title', 'Solicitar vínculo familiar')
@section('page-title', 'Solicitar vínculo familiar')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 space-y-6">
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('memberpanel.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Painel</a>
                <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                <a href="{{ route('memberpanel.relationships.pending') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Vínculos</a>
                <x-icon name="chevron-right" class="w-3 h-3 shrink-0" />
                <span class="text-gray-900 dark:text-white font-medium">Solicitar vínculo</span>
            </nav>

            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Solicitar vínculo familiar</h1>
                <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">Informe o parentesco e busque pelo CPF do membro. A pessoa receberá um convite e poderá aceitar ou recusar em Vínculos.</p>
            </div>

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                <form action="{{ route('memberpanel.relationships.store') }}" method="POST" class="p-6 space-y-6"
                      x-data="relationshipCreateForm()"
                      @submit="window.dispatchEvent(new CustomEvent('loading-overlay:show', { detail: { message: 'Enviando solicitação...' } }))">
                    @csrf

                    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3 flex items-start gap-2">
                        <x-icon name="information-circle" class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                        <p class="text-xs text-amber-800 dark:text-amber-200">Para vincular um <strong>membro</strong>, digite o CPF e clique em Buscar — ele receberá um convite para aceitar. Se a pessoa não for membro, use o campo "Nome (não membro)" e o vínculo será adicionado sem convite.</p>
                    </div>

                    <div>
                        <label for="relationship_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Parentesco *</label>
                        <select name="relationship_type" id="relationship_type" required
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            @foreach (\App\Models\UserRelationship::relationshipTypeLabels() as $value => $label)
                                <option value="{{ $value }}" {{ old('relationship_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CPF do parente (membro)</label>
                        <div class="flex gap-2">
                            <input type="text" placeholder="000.000.000-00" x-model="cpfQuery"
                                   data-mask="cpf"
                                   class="flex-1 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <button type="button" @click="searchByCpf()" :disabled="loading"
                                    class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold disabled:opacity-50 flex items-center gap-2">
                                <x-icon name="search" class="w-4 h-4" x-show="!loading" />
                                <span x-show="loading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                                Buscar
                            </button>
                        </div>
                        <input type="hidden" name="related_user_id" :value="relatedUserId">
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400" x-show="error" x-text="error"></p>
                        <div x-show="foundUser" x-cloak class="mt-3 flex items-center gap-3 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                            <template x-if="foundUser">
                                <div class="flex items-center gap-3 w-full">
                                    <img x-show="foundUser.photo" :src="foundUser.photo" alt="" class="w-12 h-12 rounded-full object-cover border-2 border-emerald-200">
                                    <div x-show="!foundUser.photo" class="w-12 h-12 rounded-full bg-emerald-200 dark:bg-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-300 font-bold text-lg" x-text="(foundUser.name || '').charAt(0)"></div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white block truncate" x-text="foundUser ? foundUser.name : ''"></span>
                                        <span class="text-xs text-gray-500 dark:text-slate-400 block truncate" x-text="foundUser ? foundUser.email : ''"></span>
                                    </div>
                                    <button type="button" @click="clearMember()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-slate-700 shrink-0" title="Limpar">
                                        <x-icon name="xmark" class="w-5 h-5" />
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="!relatedUserId">
                        <label for="related_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome (se não for membro)</label>
                        <input type="text" name="related_name" id="related_name" value="{{ old('related_name') }}"
                               placeholder="Ex: Maria Silva"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-sm transition-colors">
                            <x-icon name="paper-plane" class="w-5 h-5" />
                            Enviar solicitação
                        </button>
                        <a href="{{ route('memberpanel.relationships.pending') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <x-icon name="arrow-left" class="w-4 h-4" />
                            Voltar para Vínculos
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('alpine:init', function() {
        Alpine.data('relationshipCreateForm', function() {
            const searchUrl = '{{ route('memberpanel.relationships.search-cpf') }}';
            return {
                cpfQuery: '{{ old('cpf_query', '') }}',
                relatedUserId: '{{ old('related_user_id', '') }}',
                relatedName: '{{ old('related_name', '') }}',
                loading: false,
                error: '',
                foundUser: null,
                searchByCpf() {
                    var cpf = (this.cpfQuery || '').replace(/\D/g, '');
                    if (cpf.length !== 11) {
                        this.error = 'Informe um CPF válido com 11 dígitos.';
                        this.foundUser = null;
                        this.relatedUserId = '';
                        return;
                    }
                    this.loading = true;
                    this.error = '';
                    fetch(searchUrl + '?cpf=' + encodeURIComponent(cpf))
                        .then(function(r) { return r.json(); })
                        .then(function(d) {
                            this.loading = false;
                            if (d.data) {
                                this.foundUser = d.data;
                                this.relatedUserId = d.data.id;
                            } else {
                                this.foundUser = null;
                                this.relatedUserId = '';
                                this.error = d.message || 'Nenhum membro encontrado com este CPF.';
                            }
                        }.bind(this))
                        .catch(function() {
                            this.loading = false;
                            this.error = 'Erro ao buscar. Tente novamente.';
                            this.foundUser = null;
                            this.relatedUserId = '';
                        }.bind(this));
                },
                clearMember() {
                    this.foundUser = null;
                    this.relatedUserId = '';
                    this.cpfQuery = '';
                    this.error = '';
                }
            };
        });
    });
    </script>
    @endpush
@endsection
