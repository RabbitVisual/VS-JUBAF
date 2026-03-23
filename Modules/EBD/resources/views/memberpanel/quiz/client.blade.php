@extends('memberpanel::components.layouts.master')

@section('title', 'Quiz EBD - Em Resposta')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-950 transition-colors duration-200 pb-12" x-data="quizClient()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 min-h-[85vh] flex flex-col items-center justify-center py-8 sm:py-12 space-y-8 sm:space-y-12">

    <!-- Status: Waiting (Professor preparing) -->
    <div x-show="status === 'waiting'"
         x-transition:enter="transition ease-out duration-700"
         x-transition:enter-start="opacity-0 translate-y-12"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="text-center space-y-10 max-w-lg w-full">

        <div class="relative inline-block group">
            <div class="absolute -inset-4 bg-amber-500/20 blur-2xl rounded-full group-hover:bg-amber-500/40 transition-all animate-pulse"></div>
            <div class="relative w-40 h-40 bg-slate-900 border border-amber-500/30 rounded-[3rem] flex items-center justify-center shadow-[0_30px_60px_rgba(245,158,11,0.2)]">
                <x-icon name="hourglass-clock" style="duotone" class="w-16 h-16 text-amber-500 animate-[spin_4s_linear_infinite]" />
            </div>
            <div class="absolute -top-4 -right-4 w-12 h-12 bg-amber-600 rounded-full border-8 border-slate-950 flex items-center justify-center shadow-2xl">
                <div class="w-3 h-3 bg-white rounded-full animate-ping"></div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-600/10 text-amber-500 rounded-lg border border-amber-500/20">
                <span class="text-[10px] font-black uppercase tracking-[0.2em]">Conexão Estabelecida</span>
            </div>
            <h2 class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter italic leading-none">Aguarde...</h2>
            <p class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-[0.15em] text-[10px] max-w-xs mx-auto">Sincronizando com a mesa do professor para o próximo desafio.</p>
        </div>

        <div class="bg-white dark:bg-gray-900 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-600/5 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex items-center gap-5 text-left">
                <div class="w-14 h-14 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-600 border border-blue-500/20 shadow-xl">
                    <x-icon name="user-graduate" style="duotone" class="w-6 h-6" />
                </div>
                <div>
                    <span class="block text-[10px] font-black uppercase text-gray-400 tracking-widest">Identidade Confirmada</span>
                    <span class="text-lg font-black text-gray-900 dark:text-white italic tracking-tight">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Status: Active / Finished -->
    <div x-show="status === 'active' || status === 'finished'"
         class="w-full max-w-3xl space-y-12"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <!-- Header Info -->
        <div class="text-center space-y-6">
            <div class="inline-flex items-center gap-4 px-8 py-3 bg-slate-900 rounded-2xl border border-white/5 shadow-2xl">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500">Live Quiz</span>
                <div class="w-1.5 h-1.5 bg-amber-600 rounded-full shadow-[0_0_10px_rgba(245,158,11,1)]"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500">Questão <span x-text="questionIndex + 1">0</span></span>
            </div>

            <h3 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tighter italic leading-tight"
                x-text="status === 'finished' ? 'Jornada Concluída!' : 'O que diz a Palavra?'"></h3>
        </div>

        <!-- Submission Feedback -->
        <div x-show="hasAnswered && status === 'active'"
             x-transition.opacity
             class="fixed inset-0 z-100 bg-slate-950/95 backdrop-blur-3xl flex items-center justify-center">
            <div class="text-center space-y-10 p-16 max-w-md bg-slate-900 rounded-[3rem] border border-white/10 shadow-[0_50px_100px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-600/10 rounded-full blur-3xl"></div>

                <div class="relative z-10 w-28 h-28 bg-emerald-500 rounded-4xl flex items-center justify-center mx-auto shadow-2xl shadow-emerald-600/30 animate-[scale-in_0.4s_ease-out]">
                    <x-icon name="vial-circle-check" style="duotone" class="w-14 h-14 text-white" />
                </div>

                <div class="relative z-10 space-y-3">
                    <h3 class="text-4xl font-black text-white italic tracking-tighter uppercase leading-none">Registrado!</h3>
                    <p class="text-gray-400 font-bold uppercase tracking-[0.2em] text-[10px]">Aguardando o desfecho da rodada...</p>
                </div>

                <div class="relative z-10 w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 animate-[progress_2s_linear_infinite]"></div>
                </div>
            </div>
        </div>

        <!-- Choice Grid -->
        <div class="grid grid-cols-2 gap-6 md:gap-8 h-[60vh] max-h-[700px]">
            <!-- A - Red (Triangle) -->
            <button @click="submitAnswer('A')"
                    :disabled="hasAnswered || status === 'finished'"
                    class="group relative bg-rose-600 rounded-2xl sm:rounded-[2.5rem] shadow-2xl shadow-rose-900/30 flex flex-col items-center justify-center border-b-4 sm:border-b-8 border-rose-800 active:border-b-0 active:translate-y-1 sm:active:translate-y-2 transition-all disabled:opacity-40 disabled:grayscale disabled:cursor-not-allowed overflow-hidden touch-manipulation">
                <div class="absolute top-4 sm:top-6 left-4 sm:left-8 text-white/10 font-black text-5xl sm:text-7xl italic pointer-events-none">A</div>
                <div class="relative z-10 w-24 h-24 bg-white/10 rounded-3xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <x-icon name="triangle" style="duotone" class="w-12 h-12 text-white" />
                </div>
            </button>

            <!-- B - Blue (Diamond) -->
            <button @click="submitAnswer('B')"
                    :disabled="hasAnswered || status === 'finished'"
                    class="group relative bg-blue-600 rounded-2xl sm:rounded-[2.5rem] shadow-2xl shadow-blue-900/30 flex flex-col items-center justify-center border-b-4 sm:border-b-8 border-blue-800 active:border-b-0 active:translate-y-1 sm:active:translate-y-2 transition-all disabled:opacity-40 disabled:grayscale disabled:cursor-not-allowed overflow-hidden touch-manipulation">
                <div class="absolute top-6 left-8 text-white/10 font-black text-7xl italic pointer-events-none">B</div>
                <div class="relative z-10 w-24 h-24 bg-white/10 rounded-3xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <x-icon name="diamond" style="duotone" class="w-12 h-12 text-white" />
                </div>
            </button>

            <!-- C - Yellow (Circle) -->
            <button @click="submitAnswer('C')"
                    :disabled="hasAnswered || status === 'finished'"
                    class="group relative bg-amber-500 rounded-2xl sm:rounded-[2.5rem] shadow-2xl shadow-amber-900/30 flex flex-col items-center justify-center border-b-4 sm:border-b-8 border-amber-600 active:border-b-0 active:translate-y-1 sm:active:translate-y-2 transition-all disabled:opacity-40 disabled:grayscale disabled:cursor-not-allowed overflow-hidden touch-manipulation">
                <div class="absolute top-6 left-8 text-white/10 font-black text-7xl italic pointer-events-none">C</div>
                <div class="relative z-10 w-24 h-24 bg-white/10 rounded-3xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <x-icon name="circle" style="duotone" class="w-12 h-12 text-white" />
                </div>
            </button>

            <!-- D - Green (Square) -->
            <button @click="submitAnswer('D')"
                    :disabled="hasAnswered || status === 'finished'"
                    class="group relative bg-emerald-600 rounded-2xl sm:rounded-[2.5rem] shadow-2xl shadow-emerald-900/30 flex flex-col items-center justify-center border-b-4 sm:border-b-8 border-emerald-800 active:border-b-0 active:translate-y-1 sm:active:translate-y-2 transition-all disabled:opacity-40 disabled:grayscale disabled:cursor-not-allowed overflow-hidden touch-manipulation">
                <div class="absolute top-6 left-8 text-white/10 font-black text-7xl italic pointer-events-none">D</div>
                <div class="relative z-10 w-24 h-24 bg-white/10 rounded-3xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <x-icon name="square" style="duotone" class="w-12 h-12 text-white" />
                </div>
            </button>
        </div>

        <!-- Visual Progress -->
        <div class="flex justify-center gap-4" x-show="status === 'active'">
            <template x-for="i in 5">
                <div class="h-2 w-16 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-inner border border-gray-200 dark:border-gray-700">
                    <div class="h-full bg-linear-to-r from-amber-600 to-amber-400 transition-all duration-700 shadow-[0_0_10px_rgba(245,158,11,0.5)]"
                         :class="i <= (questionIndex + 1) ? 'w-full' : 'w-0'"></div>
                </div>
            </template>
        </div>
    </div>
    </div>
</div>

<style>
    @keyframes scale-in {
        0% { transform: scale(0.6); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes progress {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
</style>

@push('scripts')
<script>
function quizClient() {
    return {
        status: 'waiting',
        questionIndex: 0,
        hasAnswered: false,
        sessionId: '{{ $sessionId }}',
        pollingInterval: null,

        init() {
            this.startPolling();
        },

        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.checkStatus();
            }, 2500); // Slightly faster polling for tighter sync
        },

        async checkStatus() {
            try {
                const response = await fetch(`/api/v1/ebd/quiz/status/${this.sessionId}`);
                const json = await response.json();
                const data = json.data || json;

                if (data.status !== this.status || data.current_question_index !== this.questionIndex) {
                    if (data.status === 'active' && data.current_question_index !== this.questionIndex) {
                        this.hasAnswered = false;
                        if (navigator.vibrate) navigator.vibrate([80, 40, 80]);
                    }
                    this.status = data.status;
                    this.questionIndex = data.current_question_index;
                }
            } catch (err) {
                console.error('Quiz Polling Error:', err);
            }
        },

        async submitAnswer(answer) {
            if (this.hasAnswered) return;

            this.hasAnswered = true;
            if (navigator.vibrate) navigator.vibrate(100);

            try {
                const response = await fetch(`/api/v1/ebd/quiz/submit/${this.sessionId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        answer: answer,
                        student_id: {{ auth()->id() }}
                    })
                });

                if (!response.ok) throw new Error('Failed to submit');

            } catch (err) {
                console.error('Submit Error:', err);
                this.hasAnswered = false;
                if (window.Toast) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Erro ao enviar resposta. Tente novamente.'
                    });
                }
            }
        }
    }
}
</script>
@endpush
@endsection

