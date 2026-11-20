<?php

declare(strict_types=1);

use DbCms\Core\Services\Cms\ThemeManagerService;
use Illuminate\Support\Facades\Blade;
use Tests\Support\CmsFixtures;

/** Optional disposable browser artifacts, written only to a caller-supplied directory. */
function writeContentRegionFixture(string $name, string $html): void
{
    $directory = getenv('DBCMS_REGION_FIXTURE_DIR');
    if (! is_string($directory) || $directory === '') {
        return;
    }
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    file_put_contents($directory.'/'.$name.'.html', $html);
}

beforeEach(function (): void {
    if (! getenv('DBCMS_REGION_FIXTURE_DIR')) {
        $this->withoutVite();
    }
    config(['cms.page_cache.enabled' => false]);
    CmsFixtures::localization(['es'], ['es']);
});

test('every public Page theme leaves effective section roots directly in its content region', function (): void {
    $page = CmsFixtures::home();
    $sections = [
        ['id' => 'region-viewport', 'type' => 'accordion', 'appearance' => ['extent' => 'viewport'], 'items' => [['title' => 'Viewport', 'children' => [['id' => 'wide-heading', 'type' => 'heading', 'text' => 'Región de ancho completo']]]]],
        ['id' => 'region-theme', 'type' => 'accordion', 'appearance' => ['extent' => 'theme'], 'items' => [['title' => 'Theme', 'children' => [['id' => 'nested-viewport', 'type' => 'accordion', 'appearance' => ['extent' => 'viewport'], 'items' => [['title' => 'Nested', 'children' => [['id' => 'nested-heading', 'type' => 'heading', 'text' => 'Contenido anidado']]]]]]]]],
        ['id' => 'region-columns', 'type' => 'text', 'columns_layout' => '2', 'appearance' => ['extent' => 'viewport'], 'columns' => [
            ['id' => 'left-column', 'blocks' => [['id' => 'left-text', 'type' => 'rich_text', 'content' => 'Columna primera']]],
            ['id' => 'right-column', 'blocks' => [['id' => 'right-text', 'type' => 'rich_text', 'content' => 'Columna segunda']]],
        ]],
    ];
    $ids = [];
    foreach ($sections as $order => $payload) {
        $section = $page->sections()->create(['type' => $payload['type'], 'order' => $order, 'is_active' => true, 'status' => 'published']);
        $section->translations()->create(['locale' => 'es', 'content_json' => $payload]);
        $ids[] = $section->getKey();
    }
    foreach (['db-minimal'] as $theme) {
        config(['cms.theme' => $theme]);
        app(ThemeManagerService::class)->syncActiveTheme();
        $response = $this->get('/es/es')->assertOk();
        $html = (string) $response->getContent();
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        foreach ($ids as $index => $id) {
            $root = $xpath->query('//*[@data-cms-section="'.$id.'"]')->item(0);
            expect($root)->toBeInstanceOf(DOMElement::class);
            expect($root->parentNode->attributes->getNamedItem('data-cms-content-region'))->not->toBeNull();
            expect($root->getAttribute('data-cms-extent'))->toBe($index === 1 ? 'theme' : 'viewport');
        }
        expect($xpath->query('//main')->length)->toBe(1);
        writeContentRegionFixture($theme.'-page', $html);

        // Exercise an ordinary module-shaped view in the real theme layout.
        $module = Blade::render("@extends('theme-{$theme}::layouts.app') @section('content')<article id=\"module-content\"><h1>Contenido de módulo</h1><p>Su ancho de tema debe conservarse.</p></article>@endsection", ['currentLocale' => 'es', 'seoMeta' => null]);
        writeContentRegionFixture($theme.'-module', $module);
        expect($module)->toContain('id="module-content"');
    }
});
