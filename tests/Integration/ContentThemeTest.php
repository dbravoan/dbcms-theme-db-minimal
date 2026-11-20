<?php

declare(strict_types=1);

use DbCms\Core\Contracts\Services\LocalizationServiceInterface;
use DbCms\Core\Models\Market;
use DbCms\Modules\Gallery\Models\Gallery;
use Tests\Support\BlogFixtures;
use Tests\Support\CmsFixtures;

beforeEach(function (): void {
    $this->withoutVite();
    config(['cms.theme' => 'db-minimal', 'cms.page_cache.enabled' => false]);
    CmsFixtures::localization(['es', 'en']);
    CmsFixtures::identities();
    CmsFixtures::home();
    BlogFixtures::post();
    $gallery = Gallery::create(['title' => 'Galería pública', 'slug' => 'galeria-publica', 'is_active' => true]);
    $gallery->translations()->create(['locale' => 'es', 'title' => 'Galería pública', 'slug' => 'galeria-publica', 'description' => 'Descripción pública']);
    $gallery->markets()->sync(Market::pluck('id')->all());
});

test('optional Blog and Gallery use the owned listing and detail views', function (): void {
    $galleryPrefix = app(LocalizationServiceInterface::class)->getModulePrefix('gallery', 'es');
    foreach (['/es/es/blog', '/es/es/blog/arquitectura-modular', '/es/es/blog/arquitectura', '/es/es/'.$galleryPrefix, '/es/es/'.$galleryPrefix.'/galeria-publica'] as $url) {
        $this->get($url)->assertOk()->assertSee('front-db-minimal', false);
    }
});

test('the theme preserves its design identity on optional content pages', function (): void {
    $html = $this->get('/es/es/blog')->assertOk()->getContent();
    expect($html)->toContain('tracking-widest');
    preg_match('/<main\b[^>]*>(.*?)<\/main>/s', $html, $main);
    expect($main[1] ?? $html)->not->toContain('cyber-card', '#00FF99');
});
