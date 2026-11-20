@extends('theme-db-minimal::layouts.app')

@section('title', ($translation->meta_title ?? $translation->name) . ' - dbCMS Minimal')
@section('meta_description', $translation->meta_description ?? $translation->summary ?? '')

@section('content')
<div class="space-y-16" x-data="{ activeImage: @js($product->featured_image ?? '') }">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-mono text-neutral-400">
        <a href="{{ route('shop.index', ['market' => $currentMarket->code, 'locale' => $currentLocale]) }}" class="hover:text-neutral-900">{{ __(__('Catálogo')) }}</a>
        <span>/</span>
        @if($product->category)
            <span>{{ $product->category->localizedName() }}</span>
            <span>/</span>
        @endif
        <span class="text-neutral-900 font-medium">{{ $translation->name }}</span>
    </nav>

    <!-- Product Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

        <!-- Left: Image Gallery -->
        <div class="space-y-4">
            <div class="minimal-card h-96 sm:h-[480px] bg-neutral-50 overflow-hidden flex items-center justify-center p-4">
                <template x-if="activeImage">
                    <img :src="activeImage" alt="{{ $translation->name }}" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
                </template>
                <template x-if="!activeImage">
                    <div class="text-neutral-300 text-6xl font-light">◻</div>
                </template>
            </div>

            <!-- Thumbnail Switcher -->
            @if(!empty($product->gallery_images) && count($product->gallery_images) > 0)
                <div class="flex items-center gap-2 overflow-x-auto pb-2">
                    @if($product->featured_image)
                        <button type="button" @click="activeImage = @js($product->featured_image)" class="w-16 h-16 rounded overflow-hidden border bg-neutral-50 shrink-0 transition-all" :class="activeImage === @js($product->featured_image) ? 'border-neutral-900' : 'border-neutral-200 opacity-60 hover:opacity-100'">
                            <img src="{{ $product->featured_image }}" class="w-full h-full object-cover" loading="lazy" decoding="async">
                        </button>
                    @endif
                    @foreach($product->gallery_images as $gImg)
                        <button type="button" @click="activeImage = @js($gImg)" class="w-16 h-16 rounded overflow-hidden border bg-neutral-50 shrink-0 transition-all" :class="activeImage === @js($gImg) ? 'border-neutral-900' : 'border-neutral-200 opacity-60 hover:opacity-100'">
                            <img src="{{ $gImg }}" class="w-full h-full object-cover" loading="lazy" decoding="async">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Specs, Pricing & Purchase / Quote Action Box -->
        <div class="minimal-card p-6 sm:p-8 space-y-6">
            <div class="space-y-2 border-b border-neutral-100 pb-5">
                <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-widest text-neutral-400">
                    <span>{{ $product->category?->localizedName() ?? 'General' }}</span>
                    <span>SKU: {{ $product->sku }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-light text-neutral-900 leading-tight">
                    {{ $translation->name }}
                </h1>
            </div>

            <!-- Short Summary -->
            <p class="text-neutral-600 text-sm font-light leading-relaxed">
                {{ $translation->summary ?? 'Diseño escandinavo de líneas puras y funcionalidad esencial.' }}
            </p>

            <!-- Actions: Cart vs Quote -->
            @include('shop::partials.product-offer')

            <!-- Technical Specifications -->
            @include('shop::partials.product-specifications')

        </div>

    </div>

    <!-- Modular Sections & Detailed Description -->
    @if(isset($renderedSections) && count($renderedSections) > 0)
        <section class="space-y-12">
            <h2 class="text-xl font-light text-neutral-900 border-b border-neutral-200 pb-3">
                Detalles del Objeto
            </h2>
            @foreach($renderedSections as $sectionHtml)
                <div>
                    {!! $sectionHtml !!}
                </div>
            @endforeach
        </section>
    @elseif(!empty($translation->description))
        <section class="minimal-card p-8 font-light text-neutral-700 text-sm leading-relaxed max-w-none">
            {!! app(\DbCms\Core\Services\Media\MarkdownRendererService::class)->render((string) $translation->description) !!}
        </section>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <section class="space-y-6">
            <h2 class="text-base font-mono uppercase tracking-widest text-neutral-900 border-b border-neutral-200 pb-3">
                Relacionados
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 font-mono text-xs">
                @foreach($relatedProducts as $rel)
                    @php
                        $relTrans = public_translation($rel, $currentLocale, $currentMarket ?? null);
                        $shopPrefix = app(\DbCms\Core\Contracts\Services\LocalizationServiceInterface::class)->getModulePrefix('shop', $currentLocale) ?: 'shop';
                        $relUrl = app(\DbCms\Core\Contracts\Services\LocalizationServiceInterface::class)->url("/{$shopPrefix}/{$relTrans?->slug}", $currentMarket);
                    @endphp
                    <a href="{{ $relUrl }}" class="minimal-card p-4 hover:border-neutral-400 transition-all block space-y-2 group">
                        <div class="h-32 bg-neutral-50 rounded overflow-hidden flex items-center justify-center">
                            @if($rel->featured_image)
                                <img src="{{ $rel->featured_image }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy" decoding="async">
                            @else
                                <span class="text-2xl text-neutral-300">◻</span>
                            @endif
                        </div>
                        <h4 class="font-medium text-neutral-900 group-hover:underline truncate">{{ $relTrans?->name }}</h4>
                        @include('shop::partials.product-price', ['product' => $rel])
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Inquiry Modal -->


</div>

@endsection
