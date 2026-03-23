@extends('layouts.print')

@section('title', 'Escala - ' . $setlist->title)

@push('styles')
<style>
    @media print {
        body { background: white; }
    }
    body { font-family: 'Inter', sans-serif; background: #f3f4f6; }
    .sheet { background: white; max-width: 210mm; margin: 40px auto; padding: 20mm; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
</style>
@endpush

@section('content')
    <div class="no-print fixed top-4 right-4 flex gap-4">
        <button type="button" onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold shadow hover:bg-blue-700">Imprimir</button>
        <button type="button" onclick="window.close()" class="bg-gray-600 text-white px-4 py-2 rounded font-bold shadow hover:bg-gray-700">Fechar</button>
    </div>

    <div class="sheet">
        <div class="text-center mb-10 border-b-2 border-gray-200 pb-6">
            <h1 class="text-3xl font-black uppercase text-gray-900 mb-2">{{ $setlist->title }}</h1>
            <p class="text-lg text-gray-600">{{ $setlist->scheduled_at->format('l, d \d\e F \d\e Y \à\s H:i') }}</p>
            <p class="text-sm text-gray-500 mt-2">Dirigente: <span class="font-bold">{{ $setlist->leader->name }}</span></p>
        </div>

        <div class="grid grid-cols-2 gap-12">
            <div>
                <h2 class="text-xl font-bold uppercase border-b border-gray-800 mb-4 pb-2">Repertório</h2>
                <ul class="space-y-4">
                    @foreach($setlist->items as $index => $item)
                    <li class="flex items-start justify-between">
                        <div class="flex gap-3">
                            <span class="font-bold text-gray-400 w-6 text-right">{{ $index + 1 }}.</span>
                            <div>
                                <strong class="block text-gray-900">{{ $item->song->title }}</strong>
                                <span class="text-sm text-gray-600">{{ $item->song->artist }}</span>
                            </div>
                        </div>
                        <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $item->key }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-bold uppercase border-b border-gray-800 mb-4 pb-2">Escala (Músicos)</h2>
                <ul class="space-y-4">
                    @forelse($setlist->roster as $roster)
                    <li class="flex items-center justify-between">
                        <span class="text-gray-900 font-medium">{{ $roster->user->name }}</span>
                        <span class="text-sm text-gray-500 italic">{{ $roster->instrument->name }}</span>
                    </li>
                    @empty
                    <li class="text-gray-500 italic">Nenhum músico escalado.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        @if($setlist->description)
        <div class="mt-12 p-6 bg-gray-50 rounded-xl border border-gray-200">
            <h3 class="font-bold text-gray-900 mb-2 uppercase text-sm">Observações</h3>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $setlist->description }}</p>
        </div>
        @endif

        <div class="mt-20 text-center text-xs text-gray-400">
            Gerado pelo VertexCBAV em {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
@endsection
