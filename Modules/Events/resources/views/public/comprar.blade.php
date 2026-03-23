@extends('homepage::components.layouts.master')

@section('title', 'Garantir vaga — ' . $event->title)

@section('content')
<div class="bg-gray-50 dark:bg-gray-950 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-6xl mb-8">
        <a href="{{ route('events.public.show', $event->slug) }}" class="inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
            <x-icon name="arrow-left" style="duotone" class="w-5 h-5" />
            Voltar ao evento
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $event->title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Preencha os dados e escolha a forma de pagamento.</p>
    </div>

    <div x-data="eventRegistrationComprar" class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <form id="registration-form" action="{{ route('events.public.register', $event) }}" method="POST">
                    @csrf

                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <x-icon name="ticket" style="duotone" class="w-6 h-6 text-indigo-500" />
                            Selecione o ingresso
                        </h2>
                        <div class="space-y-4">
                            @foreach($event->priceRules as $rule)
                                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                    <div>
                                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $rule->label }}</h3>
                                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">R$ {{ number_format($rule->price, 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="updateQuantity('{{ $rule->id }}', -1)" :disabled="getQuantity('{{ $rule->id }}') <= 0" class="w-10 h-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center disabled:opacity-50">
                                            <x-icon name="minus" class="w-4 h-4" />
                                        </button>
                                        <span class="w-12 text-center font-bold" x-text="getQuantity('{{ $rule->id }}')"></span>
                                        <button type="button" @click="updateQuantity('{{ $rule->id }}', 1)" class="w-10 h-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center">
                                            <x-icon name="plus" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <template x-for="(participant, index) in participants" :key="index">
                        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-6 mb-8 relative overflow-hidden">
                            <div class="absolute left-0 top-0 w-1 h-full bg-indigo-500"></div>
                            <div class="pl-4">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Participante <span x-text="index + 1"></span></h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome completo</label>
                                        <input type="text" :name="'participants['+index+'][name]'" x-model="participant.name" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white p-3" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                                        <input type="email" :name="'participants['+index+'][email]'" x-model="participant.email" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white p-3" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CPF</label>
                                        <input type="text" :name="'participants['+index+'][document]'" x-model="participant.document" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white p-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                                        <input type="text" :name="'participants['+index+'][phone]'" x-model="participant.phone" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white p-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de nascimento</label>
                                        <input type="date" :name="'participants['+index+'][birth_date]'" x-model="participant.birth_date" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white p-3" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="totalAmount > 0" class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-8 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <x-icon name="credit-card" style="duotone" class="w-6 h-6 text-emerald-500" />
                            Forma de pagamento
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            @foreach($gateways as $gateway)
                                <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-indigo-500 {{ $gateway->isConfigured() ? '' : 'opacity-50' }}">
                                    <input type="radio" name="payment_gateway_id" value="{{ $gateway->id }}" data-name="{{ $gateway->name }}" class="sr-only peer gateway-input" {{ $gateway->isConfigured() ? '' : 'disabled' }} :required="totalAmount > 0">
                                    <div class="flex-1 font-bold text-gray-900 dark:text-white">{{ $gateway->display_name }}</div>
                                    <div class="w-5 h-5 rounded-full border-2 peer-checked:bg-indigo-600 peer-checked:border-indigo-600"></div>
                                </label>
                            @endforeach
                        </div>
                        <input type="hidden" name="payment_method" id="method_input">
                        <input type="hidden" name="brick_payload" id="brick_payload">
                        @error('payment_gateway_id')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="submit-button-container" x-show="totalParticipants > 0">
                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-2xl font-bold text-lg flex items-center justify-center gap-2">
                            <x-icon name="credit-card" style="duotone" class="w-6 h-6" />
                            <span>Prosseguir para pagamento</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Resumo</h3>
                    <template x-for="(qty, ruleId) in quantities" :key="ruleId">
                        <div x-show="qty > 0" class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                            <span x-text="getRuleName(ruleId) + ' (x' + qty + ')'"></span>
                            <span x-text="formatCurrency(getRulePrice(ruleId) * qty)"></span>
                        </div>
                    </template>
                    <div class="flex justify-between font-bold text-gray-900 dark:text-white pt-4 border-t border-gray-100 dark:border-gray-800 mt-4">
                        <span>Total</span>
                        <span class="text-xl text-indigo-600 dark:text-indigo-400" x-text="formatCurrency(totalAmount)"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('eventRegistrationComprar', () => ({
        quantities: {},
        priceRules: @json($event->priceRules),
        hasPriceRules: true,
        user: @json($defaultParticipant ?? null),
        participants: [],
        totalAmount: 0,

        init() {
            this.priceRules.forEach(rule => { this.quantities[rule.id] = 0; });
            this.$watch('quantities', () => this.updateState(), { deep: true });
            this.updateState();
        },

        getQuantity(ruleId) { return this.quantities[ruleId] || 0; },
        updateQuantity(ruleId, change) {
            const v = this.getQuantity(ruleId) + change;
            this.quantities[ruleId] = Math.max(0, v);
        },
        canAddMore() { return true; },
        get totalParticipants() { return Object.values(this.quantities).reduce((a,b) => a+b, 0); },
        getRuleName(ruleId) { const r = this.priceRules.find(x => x.id == ruleId); return r ? r.label : 'Ingresso'; },
        getRulePrice(ruleId) { const r = this.priceRules.find(x => x.id == ruleId); return r ? parseFloat(r.price) || 0 : 0; },

        updateState() {
            let total = 0;
            this.priceRules.forEach(rule => { total += this.getRulePrice(rule.id) * (this.quantities[rule.id] || 0); });
            this.totalAmount = total;
            const n = this.totalParticipants;
            const target = [];
            this.priceRules.forEach(rule => {
                const q = this.quantities[rule.id] || 0;
                for (let i = 0; i < q; i++) target.push({ name: rule.label });
            });
            while (this.participants.length > n) this.participants.pop();
            while (this.participants.length < n) {
                this.participants.push({ name: '', email: '', document: '', phone: '', birth_date: '', ticketName: target[this.participants.length]?.name || 'Ingresso', isMe: false });
            }
            this.participants.forEach((p, i) => { p.ticketName = target[i]?.name || 'Ingresso'; });
        },

        formatCurrency(v) { return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v); },
    }));
});
</script>
@endpush
@endsection
