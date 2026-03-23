@extends('homepage::components.layouts.master')

@section('footer')
    @include('marketplace::components.footer')
    {{-- Sticky cart (mobile only) --}}
    <a href="{{ route('marketplace.storefront.cart') }}" class="md:hidden fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/30" aria-label="{{ __('marketplace::messages.cart') }}">
        <x-icon name="cart-shopping" style="duotone" class="w-6 h-6" />
        @if(($cartCount ?? 0) > 0)
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
        @endif
    </a>
@endsection
