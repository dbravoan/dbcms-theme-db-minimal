@extends('theme-db-minimal::layouts.app')

@php
    $trans = public_translation($post);
    $marketCode = $currentMarket?->code ?? 'es';
    $markdownRenderer = app(\DbCms\Core\Services\Media\MarkdownRendererService::class);
    $renderedBody = $trans ? $markdownRenderer->render($trans->content) : '';
@endphp

@section('title', ($trans->meta_title ?? $trans->title ?? __('Artículo')) . ' - dbCMS Minimal')
@section('meta_description', $trans->meta_description ?? $trans->summary ?? '')

@section('content')
<div class="max-w-3xl mx-auto space-y-12" @if(!empty($post)) data-cms-edit-url="{{ route('admin.blog.posts.edit', $post->id) }}" data-cms-edit-label="Editar Artículo" @endif>

    <!-- Breadcrumb Component -->
    @include('components.breadcrumbs', ['trail' => $breadcrumbs ?? []])

    <!-- Article Header -->
    <header class="space-y-4 border-b border-neutral-200/80 pb-8">
        <div class="flex items-center gap-3 text-[10px] font-mono uppercase tracking-widest text-neutral-400">
            <span>{{ public_translation($post->category)?->name ?? 'Minimal' }}</span>
            <span>&bull;</span>
            <span>{{ ($post->published_at ?? now())->copy()->locale(app()->getLocale())->isoFormat('LL') }}</span>
            <span>&bull;</span>
            <span>{{ $post->reading_time_minutes }}m</span>
        </div>

        <h1 class="text-3xl sm:text-4xl font-light text-neutral-900 tracking-tight leading-tight">
            {{ $trans->title ?? '' }}
        </h1>

        @if($trans->summary)
            <p class="text-base text-neutral-500 font-light leading-relaxed">
                {{ $trans->summary }}
            </p>
        @endif

        <div class="flex items-center space-x-3 pt-2 text-[11px] font-mono text-neutral-400">
            <span>Por: {{ $post->author->name ?? 'dbCMS Studio' }}</span>
        </div>
    </header>

    <!-- Featured Image -->
    @if($post->featured_image)
        <div class="border border-neutral-200 overflow-hidden">
            <img src="{{ $post->featured_image }}" alt="{{ $trans->title ?? '' }}" class="w-full max-h-[460px] object-cover" loading="eager" fetchpriority="high" decoding="async">
        </div>
    @endif

    <!-- Article Body & Modular Sections -->
    @if(isset($renderedSections) && count($renderedSections) > 0)
        <div class="space-y-10">
            @foreach($renderedSections as $sectionHtml)
                <div>
                    {!! $sectionHtml !!}
                </div>
            @endforeach
        </div>
    @else
        <article class="p-4 sm:p-6 text-neutral-800 text-sm leading-relaxed space-y-6 max-w-none font-light">
            {!! $renderedBody !!}
        </article>
    @endif

    @include('blog::partials.comments')

</div>

@endsection
