@extends('theme-db-minimal::layouts.app')

@section('title', __('shop::public.checkout_title'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8 font-sans">
    <div class="space-y-2">
        <h1 class="text-3xl font-light tracking-tight text-neutral-900">{{ __('shop::public.checkout_title') }}</h1>
        <p class="text-xs font-mono text-neutral-400">{{ __('shop::public.checkout_description') }}</p>
    </div>
    <div class="minimal-card p-6">
        @include('shop::partials.checkout-form', [
            'items' => $items,
            'subtotal' => $subtotal,
            'taxTotal' => $taxTotal,
            'grandTotal' => $grandTotal,
            'currentMarket' => $currentMarket,
            'currentLocale' => $currentLocale,
        ])
    </div>
</div>
@endsection
