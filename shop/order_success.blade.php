@extends('theme-db-minimal::layouts.app')

@section('title', __('shop::public.order_confirmed'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8 font-sans">
    <h1 class="text-3xl font-light tracking-tight text-neutral-900">{{ __('shop::public.order_confirmed') }}</h1>
    <p class="text-sm opacity-75">{{ __('shop::public.order_description', ['email' => $order->customer_email]) }}</p>
    <div class="minimal-card p-6">
        @include('shop::partials.order-summary', [
            'order' => $order,
            'currentMarket' => $currentMarket,
            'currentLocale' => $currentLocale,
        ])
    </div>
</div>
@endsection
