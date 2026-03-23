@props(['song', 'action' => null])

<div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-sm font-bold text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
            {{ $song->original_key }}
        </div>
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white text-sm line-clamp-1">{{ $song->title }}</h4>
            <p class="text-xs text-gray-500 line-clamp-1">{{ $song->artist }}</p>
        </div>
    </div>

    @if($action)
        <div class="opacity-0 group-hover:opacity-100 transition-all">
            {!! $action !!}
        </div>
    @else
        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
            @if($song->youtube_link)
                <a href="{{ $song->youtube_link }}" target="_blank" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                    <x-icon name="play" class="w-4 h-4" />
                </a>
            @endif
            <a href="{{ route('worship.admin.songs.show', $song->id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded">
                <x-icon name="eye" class="w-4 h-4" />
            </a>
        </div>
    @endif
</div>

