{{-- Painel Elias no editor de sermão: Sugerir Ilustração, Verificar Coerência, Pesquisa Histórica. --}}
@php
    $chatUrl = route('memberpanel.cbav-bot.chat');
@endphp
<div x-data="eliasSermonStudio('{{ $chatUrl }}')" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
    <button @click="open = !open" class="w-full px-4 py-3 flex items-center justify-between text-left font-medium text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
        <span class="flex items-center gap-2">
            <x-icon name="gavel" class="w-5 h-5 text-amber-500" />
            Elias – Consultor
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-xs font-bold cursor-help" title="Análise do texto nos originais e referências cruzadas para aprofundar o sentido (exegese). Insights sob demanda." aria-label="Ajuda">?</span>
        </span>
        <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="'open ? \'rotate-180\' : \'\''" />
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="border-t border-gray-200 dark:border-gray-700">
        <div class="p-4 space-y-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">Clique em uma ação para obter insights (sob demanda).</p>
            <div class="flex items-start gap-2 p-2.5 rounded-lg bg-amber-50/80 dark:bg-amber-900/20 border border-amber-200/60 dark:border-amber-700/40">
                <x-icon name="lightbulb" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                <p class="text-xs text-amber-800 dark:text-amber-200">
                    <strong>Dica:</strong> Antes de exportar para o púlpito, usa <strong>Revisar com Elias</strong> para normalizar a formatação (parágrafos, citações, estrutura). Não altera o teu texto, só organiza. Para ilustração ou contexto histórico, usa os botões abaixo.
                </p>
            </div>
            <div class="flex flex-col gap-2">
                <button type="button" @click="askElias('revise_format')" :disabled="loading"
                    class="text-left px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-900/30 disabled:opacity-50 transition-colors flex items-center gap-2">
                    <x-icon name="spell-check" class="w-4 h-4 shrink-0" />
                    Revisar com Elias (formatação para púlpito)
                </button>
                <button type="button" @click="askElias('suggest_illustration')" :disabled="loading"
                    class="text-left px-3 py-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200 text-sm font-medium hover:bg-amber-100 dark:hover:bg-amber-900/30 disabled:opacity-50 transition-colors">
                    Sugerir Ilustração
                </button>
                <button type="button" @click="askElias('check_coherence')" :disabled="loading"
                    class="text-left px-3 py-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200 text-sm font-medium hover:bg-amber-100 dark:hover:bg-amber-900/30 disabled:opacity-50 transition-colors">
                    Verificar Coerência (CBB)
                </button>
                <button type="button" @click="askElias('historical_research')" :disabled="loading"
                    class="text-left px-3 py-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-200 text-sm font-medium hover:bg-amber-100 dark:hover:bg-amber-900/30 disabled:opacity-50 transition-colors">
                    Pesquisa Histórica
                </button>
            </div>
            <template x-if="loading">
                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <span class="animate-spin inline-block w-4 h-4 border-2 border-amber-500 border-t-transparent rounded-full" aria-hidden="true"></span>
                    <x-icon name="gavel" class="w-4 h-4 text-amber-500 shrink-0" />
                    Elias está pensando...
                </p>
            </template>
            <template x-if="reply">
                <div class="mt-2 space-y-2">
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap border border-gray-100 dark:border-gray-700" x-text="reply"></div>
                    <template x-if="formattedHtml">
                        <button type="button" @click="applyFormat()"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                            <x-icon name="check" class="w-4 h-4" />
                            Aplicar formatação no editor
                        </button>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function eliasSermonStudio(chatUrl) {
        return {
            open: true,
            loading: false,
            reply: '',
            formattedHtml: null,
            async askElias(action) {
                this.loading = true;
                this.reply = '';
                this.formattedHtml = null;
                const mainPoint = (document.getElementById('title')?.value || '').trim();
                const editorEl = document.querySelector('.ql-editor');
                const excerpt = editorEl ? (editorEl.innerText || '').replace(/\s+/g, ' ').trim().slice(0, 1500) : '';
                const fullContent = editorEl ? (editorEl.innerHTML || '') : '';
                const reference = (document.querySelector('[name="bible_references[0][reference_text]"]')?.value || '').trim()
                    || (editorEl?.innerText?.slice(0, 200) || '');
                const defaultMsg = action === 'suggest_illustration' ? (mainPoint || excerpt.slice(0, 100) || 'ponto principal do sermão')
                    : (action === 'historical_research' ? (reference || 'contexto do texto') : (action === 'revise_format' ? 'Formatar sermão para o púlpito.' : 'verificar coerência CBB'));
                const payload = {
                    message: defaultMsg,
                    context: {
                        sermon_studio: true,
                        action: action,
                        main_point: mainPoint,
                        reference: reference,
                        excerpt: excerpt,
                        full_content: action === 'revise_format' ? fullContent : undefined
                    }
                };
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch(chatUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (res.ok) {
                        this.reply = data.reply || 'Sem resposta.';
                        if (data.formatted_html) this.formattedHtml = data.formatted_html;
                    } else {
                        this.reply = data.message || data.errors?.message?.[0] || 'Resposta inválida. Tente de novo.';
                    }
                } catch (e) {
                    this.reply = 'Erro ao conectar com o Elias. Verifica a ligação e tenta outra vez.';
                }
                this.loading = false;
            },
            applyFormat() {
                if (!this.formattedHtml) return;
                window.dispatchEvent(new CustomEvent('elias-apply-format', { detail: { html: this.formattedHtml } }));
                this.formattedHtml = null;
                this.reply = 'Formatação aplicada. O conteúdo do editor foi atualizado para impressão no púlpito.';
            }
        };
    }
</script>
