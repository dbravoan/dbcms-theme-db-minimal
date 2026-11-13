@extends($layout ?? 'theme-db-minimal::layouts.app')

@section('title', $translation->meta_title ?? $translation->title ?? __('Página'))
@section('meta_description', $translation->meta_description ?? '')

@section('content')
<x-cms::content-region theme="db-minimal" class="gap-y-16 pb-16" :data-cms-edit-url="!empty($page) ? route('admin.pages.edit', $page->id) : false" data-cms-edit-label="Editar Página">
    @if(isset($renderedSections) && count($renderedSections) > 0)
        @foreach($renderedSections as $sectionHtml)
            {!! $sectionHtml !!}
        @endforeach
    @else
        <div class="max-w-3xl mx-auto py-24 text-center minimal-card p-12">
            <h1 class="text-3xl font-light tracking-wide text-neutral-900 mb-3">{{ $translation->title ?? __('Página') }}</h1>
            <p class="text-neutral-400 text-xs uppercase tracking-widest">{{ __('Contenido en preparación') }}</p>
        </div>
    @endif
</x-cms::content-region>

<!-- Schema.org WebPage JSON-LD -->
@endsection
