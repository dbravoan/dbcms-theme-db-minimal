<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use DbCms\Core\Services\Cms\FrontViewResolver;
use Illuminate\Support\Facades\DB;
use Tests\Support\CmsFixtures;

beforeEach(function (): void {
    $this->withoutVite();
    config(['cms.theme' => 'db-minimal', 'cms.page_cache.enabled' => false]);
});

test('the theme renders public content without optional modules installed or queried', function (): void {
    CmsFixtures::localization(['es']);
    $page = CmsFixtures::home();
    $section = $page->sections()->create(['type' => 'heading', 'order' => 0, 'is_active' => true, 'status' => 'published']);
    $section->translations()->create(['locale' => 'es', 'content_json' => ['text' => 'Contenido independiente del tema']]);
    foreach (['blog', 'gallery', 'shop', 'popups'] as $module) {
        expect(InstalledVersions::isInstalled('dbravoan/dbcms-module-'.$module))->toBeFalse($module);
    }
    DB::enableQueryLog();
    DB::flushQueryLog();
    $html = $this->get('/')->assertOk()->assertSee('Contenido independiente del tema')->getContent();
    $queries = DB::getQueryLog();
    DB::disableQueryLog();
    expect($html)->toContain('front-db-minimal');
    expect(collect($queries)->filter(static fn (array $query): bool => preg_match('/(?:from|join) `(?:posts|post_translations|galleries|gallery_translations|products|popups)`/', $query['query']) === 1))->toBeEmpty();
});

test('only the selected theme contributes public assets and owns its view namespace', function (): void {
    expect(cms_asset_entries(true))->toContain('vendor/dbravoan/dbcms-theme-db-minimal/theme.css');
    expect(cms_asset_entries())->not->toContain('vendor/dbravoan/dbcms-theme-db-minimal/theme.css');
    $resolver = app(FrontViewResolver::class);
    expect($resolver->layout())->toBe('theme-db-minimal::layouts.app')
        ->and($resolver->resolve('shop.blocks.products', 'shop::blocks.products'))->toBe('theme-db-minimal::shop.blocks.products');
});

test('the theme emits one canonical and one alternate for each language through the shared head', function (): void {
    CmsFixtures::localization(['es', 'en']);
    CmsFixtures::home(['es', 'en']);
    $html = $this->get('/es/es')->assertOk()->getContent();
    $document = new DOMDocument;
    $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($document);
    expect($xpath->query('//head/link[@rel="canonical"]')->length)->toBe(1);
    $alternates = $xpath->query('//head/link[@rel="alternate"][@hreflang]');
    $languages = [];
    foreach ($alternates as $alternate) {
        $languages[] = $alternate->getAttribute('hreflang');
    }
    expect($languages)->not->toBeEmpty();
    expect(count(array_unique($languages)))->toBe(count($languages));
});
