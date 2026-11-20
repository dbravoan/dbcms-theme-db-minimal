<?php

declare(strict_types=1);

use DbCms\Core\Http\Middleware\CachePublicPage;
use DbCms\Core\Models\CmsModule;
use DbCms\Core\Services\Cms\PageCacheService;
use DbCms\Core\Services\Cms\ThemeManagerService;
use DbCms\Modules\Popups\Models\Popup;
use Illuminate\Support\Facades\DB;
use Tests\Support\CmsFixtures;

it('personalizes overlays on cached pages and never queries a disabled owner in each public theme', function (string $theme): void {
    $this->withoutVite();
    CmsFixtures::localization(['es']);
    $home = CmsFixtures::home();
    $other = CmsFixtures::home();
    $other->translations()->where('locale', 'es')->update(['slug' => 'other', 'title' => 'Otra página']);
    app(ThemeManagerService::class)->setActiveTheme($theme);
    config(['cms.page_cache.enabled' => true]);
    $popup = Popup::create(['mode' => 'modal', 'is_active' => true, 'is_published' => true, 'published_at' => now()->subMinute(), 'configuration' => [
        'conditions' => ['include' => ['pages' => [$home->id]]],
        'triggers' => [['type' => 'delay', 'seconds' => 0]], 'frequency' => ['mode' => 'page'],
    ]]);
    $popup->translations()->create(['locale' => 'es', 'title' => 'Aviso solo en inicio']);
    $section = $popup->sections()->create(['type' => 'heading', 'order' => 1, 'is_active' => true, 'status' => 'published', 'styling_json' => []]);
    $section->translations()->create(['locale' => 'es', 'content_json' => ['text' => 'Contenido del aviso']]);
    app(PageCacheService::class)->invalidate();
    $this->get('/es/es/home')->assertOk()->assertHeader(CachePublicPage::HIT_HEADER, 'MISS')->assertSee('Contenido del aviso');
    $this->get('/es/es/other')->assertOk()->assertDontSee('Contenido del aviso');
    $this->get('/es/es/home')->assertOk()->assertHeader(CachePublicPage::HIT_HEADER, 'HIT')->assertSee('Contenido del aviso');

    CmsModule::query()->where('identifier', 'popups')->update(['is_active' => false]);
    $this->travel(2)->seconds();
    DB::enableQueryLog();
    DB::flushQueryLog();
    $this->get('/es/es/home')->assertOk()->assertDontSee('Contenido del aviso');
    $queries = DB::getQueryLog();
    DB::disableQueryLog();
    expect(collect($queries)->filter(static fn (array $query): bool => preg_match('/(?:from|join) `(?:popups|popup_translations|popup_sections|popup_section_translations)`/', $query['query']) === 1))->toBeEmpty();
})->with(['db-minimal']);
