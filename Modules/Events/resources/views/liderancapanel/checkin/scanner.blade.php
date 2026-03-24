@extends('liderancapanel::components.layouts.master')

@section('title', 'Check-in')

@section('content')
<div class="fixed inset-0 z-[100] bg-gray-900 flex flex-col checkin-scanner" x-data="checkinScanner()" x-init="init()">
    {{-- Header --}}
    <header class="flex items-center justify-between px-4 py-3 bg-gray-800 border-b border-gray-700 shadow-md">
        <div>
            <h1 class="text-xl font-bold text-white">Scanner JUBAF</h1>
            <p class="text-xs text-gray-400">Validação na Portaria</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="toggleScanner()" :disabled="cameraBusy"
                class="inline-flex items-center justify-center gap-2 px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 min-w-[120px]"
                :class="scanning ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500'">
                <template x-if="cameraBusy && !scanning">
                    <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </template>
                <x-icon name="camera" class="w-4 h-4 shrink-0" x-show="!scanning && !cameraBusy" />
                <x-icon name="xmark" class="w-4 h-4 shrink-0" x-show="scanning" />
                <span x-text="scanning ? 'Parar' : (cameraBusy ? 'Aguarde...' : 'Iniciar Scanner')"></span>
            </button>
            <a href="{{ route('lideranca.eventos.index') }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-white hover:bg-gray-600 transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
        </div>
    </header>

    {{-- Main Area with fixed Footer/Stats and Feedback Full --}}
    <div class="relative flex-1 flex flex-col bg-black overflow-hidden">
        {{-- Camera div needs to fill the screen --}}
        <div id="reader" class="absolute inset-0 w-full h-full object-cover"></div>

        {{-- Standby overlay --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/95 z-10" x-show="!scanning && !feedback.visible" x-transition.opacity.duration.300ms>
            <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6 shadow-xl">
                <x-icon name="qrcode" class="w-12 h-12 text-gray-400" />
            </div>
            <p class="text-white font-bold text-xl">Câmera em Standby</p>
            <p class="text-gray-400 text-sm mt-2">Clique em "Iniciar Scanner" no topo.</p>
        </div>

        {{-- Scanning frame overlay --}}
        <div class="absolute inset-0 pointer-events-none z-20 flex items-center justify-center" x-show="scanning && !feedback.visible" x-transition.opacity>
            <div class="w-[70vw] h-[70vw] max-w-[300px] max-h-[300px] border-2 border-white/20 rounded-3xl relative shadow-2xl">
                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-amber-500 -mt-1 -ml-1 rounded-tl-3xl"></div>
                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-amber-500 -mt-1 -mr-1 rounded-tr-3xl"></div>
                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-amber-500 -mb-1 -ml-1 rounded-bl-3xl"></div>
                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-amber-500 -mb-1 -mr-1 rounded-br-3xl"></div>
                <div class="absolute top-0 left-0 w-full h-1 bg-amber-500 shadow-[0_0_15px_rgba(245,158,11,1)] animate-scan-line"></div>
            </div>
            <div class="absolute bottom-20 px-6 py-2 bg-black/60 backdrop-blur-md rounded-full text-white text-xs font-semibold tracking-wider uppercase border border-white/10">Aponte para o QR Code</div>
        </div>

        {{-- GIANT FEEDBACK MODAL (FULL SCREEN) --}}
        <div class="absolute inset-0 z-50 flex flex-col items-center justify-center p-6 transition-all duration-300 transform"
            x-show="feedback.visible" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            :class="{
                'bg-emerald-500': feedback.type === 'success',
                'bg-red-600': feedback.type === 'error',
                'bg-amber-500': feedback.type === 'warning'
            }"
            style="display: none;">
            
            <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center mb-6 shadow-2xl animate-pulse">
                <x-icon name="check" class="w-16 h-16 text-white" x-show="feedback.type === 'success'" />
                <x-icon name="xmark" class="w-16 h-16 text-white" x-show="feedback.type === 'error'" />
                <x-icon name="circle-exclamation" class="w-16 h-16 text-white" x-show="feedback.type === 'warning'" />
            </div>
            
            <h2 class="text-4xl sm:text-5xl text-center font-black text-white mb-2 uppercase tracking-tight leading-none drop-shadow-md" x-text="feedback.title"></h2>
            <p class="text-white/90 text-center text-lg font-medium mb-8 max-w-sm drop-shadow" x-text="feedback.message"></p>
            
            <template x-if="feedback.extra">
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-5 rounded-3xl w-full max-w-sm text-center mb-8 shadow-xl">
                    <p class="text-[10px] text-white/70 uppercase tracking-widest font-bold mb-1">Participante</p>
                    <p class="text-2xl font-black text-white truncate drop-shadow-sm mb-1" x-text="feedback.extra"></p>
                    <p class="text-sm font-bold text-white/90 truncate uppercase tracking-widest" x-text="feedback.church || 'Igreja não informada'"></p>
                </div>
            </template>
            
            <button type="button" @click="feedback.visible = false" 
                class="w-full max-w-sm py-4 bg-white/20 hover:bg-white/30 active:bg-white/40 text-white rounded-2xl font-bold text-lg uppercase tracking-wider transition-colors border border-white/30 shadow-lg">
                <span x-text="feedback.type === 'success' ? 'Ler Próximo' : 'Tentar Novamente'"></span>
            </button>
        </div>
    </div>

    {{-- Footer: Manual Input & Stats --}}
    <div class="bg-gray-800 border-t border-gray-700 p-4 pb-[max(1rem,env(safe-area-inset-bottom))] shadow-[0_-10px_40px_rgba(0,0,0,0.5)] z-40 relative">
        <div class="flex gap-2 mx-auto max-w-md w-full mb-3">
            <input type="text" x-model="manualHash" @keydown.enter.prevent="manualCheckin()" placeholder="Código manual"
                class="flex-1 px-4 py-3 bg-gray-900 border border-gray-600 rounded-xl text-white font-mono text-sm uppercase tracking-wider focus:ring-amber-500 focus:border-amber-500 placeholder-gray-500">
            <button type="button" @click="manualCheckin()" class="px-5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-gray-900 rounded-xl font-bold shadow-md transition-colors whitespace-nowrap">
                Validar
            </button>
        </div>
        <div class="flex items-center justify-between text-[11px] text-gray-400 font-medium px-2 mx-auto max-w-md uppercase tracking-wider">
            <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div><span>Lendo...</span></div>
            <div>Último: <span class="text-white font-bold inline-block max-w-[100px] truncate align-bottom normal-case" x-text="history.length ? history[0].name : '--'"></span></div>
            <div>Chegou: <span class="text-white font-bold text-base" x-text="history.length"></span></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkinScanner', () => ({
                scanner: null,
                scanning: false,
                cameraBusy: false,
                manualHash: '',
                history: [],
                stats: {
                    checkins: '-',
                    total: '-'
                },
                feedback: {
                    visible: false,
                    type: 'success',
                    title: '',
                    message: '',
                    extra: '',
                    church: ''
                },
                audioCtx: null,
                lastScannedHash: '',
                scanCooldownMs: 2500,

                init() {
                    const initAudio = () => {
                        if (!this.audioCtx) this.audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                    };
                    window.addEventListener('click', initAudio, { once: true });
                    window.addEventListener('touchstart', initAudio, { once: true });
                },

                playBeep(type) {
                    if (!this.audioCtx) return;
                    if (this.audioCtx.state === 'suspended') this.audioCtx.resume();
                    const oscillator = this.audioCtx.createOscillator();
                    const gain = this.audioCtx.createGain();
                    oscillator.connect(gain);
                    gain.connect(this.audioCtx.destination);
                    const now = this.audioCtx.currentTime;
                    if (type === 'success') {
                        oscillator.frequency.setValueAtTime(880, now);
                        oscillator.frequency.exponentialRampToValueAtTime(1320, now + 0.1);
                        gain.gain.setValueAtTime(0.25, now);
                        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.2);
                    } else {
                        oscillator.type = 'sawtooth';
                        oscillator.frequency.setValueAtTime(110, now);
                        gain.gain.setValueAtTime(0.25, now);
                        gain.gain.linearRampToValueAtTime(0.01, now + 0.3);
                    }
                    oscillator.start(now);
                    oscillator.stop(now + 0.3);
                },

                onScanSuccess(decodedText) {
                    if (this.feedback.visible) return;
                    if (this.lastScannedHash === decodedText && (Date.now() - (this._lastScanTime || 0)) < this.scanCooldownMs) return;
                    this.lastScannedHash = decodedText;
                    this._lastScanTime = Date.now();
                    this.validateTicket(decodedText);
                },

                toggleScanner() {
                    this.scanning ? this.stopScanner() : this.startScanner();
                },

                startScanner() {
                    if (typeof Html5Qrcode === 'undefined') {
                        this.showFeedback('error', 'Biblioteca não carregada', 'Html5Qrcode não encontrado. Recarregue a página.');
                        return;
                    }
                    this.cameraBusy = true;
                    this.scanner = new Html5Qrcode('reader');
                    const qrboxSize = Math.min(280, Math.max(200, Math.floor(Math.min(window.innerWidth, window.innerHeight) * 0.45)));
                    const config = { fps: 10, qrbox: qrboxSize, aspectRatio: 1 };
                    this.scanner.start({ facingMode: 'environment' }, config, this.onScanSuccess.bind(this))
                        .then(() => {
                            this.scanning = true;
                            this.cameraBusy = false;
                        })
                        .catch(err => {
                            this.cameraBusy = false;
                            this.showFeedback('error', 'Câmera', 'Não foi possível acessar a câmera. Verifique as permissões.');
                        });
                },

                stopScanner() {
                    if (this.scanner) {
                        this.scanner.stop().then(() => {
                            this.scanning = false;
                            this.scanner.clear();
                        });
                    }
                },

                manualCheckin() {
                    const hash = (this.manualHash || '').trim();
                    if (!hash) return;
                    this.validateTicket(hash);
                    this.manualHash = '';
                },

                async validateTicket(hash) {
                    try {
                        const response = await fetch("{{ route('lideranca.eventos.checkin.validate') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ ticket_hash: hash })
                        });
                        const data = await response.json();
                        const nowStr = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

                        if (data.success) {
                            this.showFeedback('success', 'Acesso Liberado!', data.ticket_type || 'Ingresso validado', data.user_name || '', data.church_name || '');
                            this.history.unshift({ name: data.user_name || 'Visitante', time: nowStr, status: 'Confirmado', type: 'success' });
                        } else {
                            const type = (data.message || '').includes('JÁ FOI UTILIZADO') ? 'warning' : 'error';
                            this.showFeedback(type, 'Rejeitado', data.message || 'Erro desconhecido');
                            this.history.unshift({ name: '—', time: nowStr, status: 'Falha', type });
                        }
                        if (this.history.length > 8) this.history.pop();
                    } catch (e) {
                        this.showFeedback('error', 'Erro', 'Falha de conexão. Tente novamente.');
                    }
                },

                showFeedback(type, title, message, extra = '', church = '') {
                    this.feedback = { visible: true, type, title, message, extra, church };
                    this.playBeep(type);
                }
            }));
        });
    </script>
    <style>
        @keyframes scan-line {
            0% { top: 0; opacity: 0; }
            5% { opacity: 1; }
            95% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }

        .animate-scan-line {
            animation: scan-line 2.5s linear infinite;
        }

        #reader {
            min-height: 100vh;
            position: absolute;
            inset: 0;
        }

        #reader video {
            height: 100vh;
            width: 100vw;
            object-fit: cover;
        }

        @media (min-width: 640px) {
            #reader__scan_region video {
                height: 100vh;
                object-fit: cover;
            }
        }
    </style>
@endpush
