@extends('theme-db-minimal::layouts.app')

@section('title', __('shop::public.cart_title') . ' - dbCMS Minimal')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 font-sans">

    <div class="flex items-center justify-between border-b border-neutral-200/80 pb-4">
        <div>
            <h1 class="text-3xl font-light text-neutral-900 tracking-tight">
                {{ __('shop::public.cart_title') }}
            </h1>
            <p class="text-xs text-neutral-400 mt-0.5 font-mono">{{ __('shop::public.cart_subtitle') }}</p>
        </div>
        <a href="{{ route('shop.index', ['market' => $currentMarket->code, 'locale' => $currentLocale]) }}" class="text-neutral-500 hover:text-neutral-900 text-xs font-mono uppercase tracking-wider">
            &larr; {{ __('shop::public.continue_shopping') }}
        </a>
    </div>

    @if(empty($items))
        <div class="p-20 text-center minimal-card space-y-4">
            <span class="text-4xl block">◻</span>
            <h3 class="text-lg font-light text-neutral-900">{{ __('shop::public.empty_cart') }}</h3>
            <a href="{{ route('shop.index', ['market' => $currentMarket->code, 'locale' => $currentLocale]) }}" class="px-5 py-2 bg-neutral-900 text-white rounded text-xs font-mono uppercase tracking-widest inline-block">{{ __('shop::public.explore_shop') }} &rarr;</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 minimal-card overflow-hidden">
                @include('shop::partials.cart-items', [
                    'items' => $items,
                    'currentMarket' => $currentMarket,
                    'currentLocale' => $currentLocale,
                ])
            </div>
            <div class="minimal-card p-6 space-y-4 h-fit font-mono text-xs">
                @include('shop::partials.cart-summary', [
                    'subtotal' => $subtotal,
                    'taxTotal' => $taxTotal,
                    'grandTotal' => $grandTotal,
                    'currentMarket' => $currentMarket,
                    'currentLocale' => $currentLocale,
                ])
            </div>
        </div>
    @endif

</div>
@endsection
