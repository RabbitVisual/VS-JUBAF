@extends('marketplace::layouts.storefront')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('marketplace.storefront.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium mb-8">
            <x-icon name="arrow-left" style="duotone" class="w-4 h-4 mr-2" /> {{ __('marketplace::messages.store') }}
        </a>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-10 border-b border-gray-200 dark:border-gray-600">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            </div>
            <div class="p-6 md:p-10 prose dark:prose-invert prose-sm max-w-none text-gray-600 dark:text-gray-400">
                @if($content)
                    {!! $content !!}
                @else
                    <p class="text-gray-500 dark:text-gray-500">{{ __('marketplace::messages.no_description') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
