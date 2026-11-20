<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use DbCms\Core\Services\Cms\ThemeManagerService;

beforeEach(function (): void {
    $this->themeId = 'db-minimal';
    $this->themePath = InstalledVersions::getInstallPath('dbravoan/dbcms-theme-db-minimal');
    $this->themes = app(ThemeManagerService::class)->getAvailableThemes();
});

test('the owned theme is discovered with all its optional view contributions', function (): void {
    expect($this->themes)->toHaveKey($this->themeId);
    foreach (['theme.json', 'theme.css', 'layouts/app.blade.php', 'partials/header.blade.php', 'partials/footer.blade.php', 'pages/show.blade.php', 'blog/index.blade.php', 'blog/category.blade.php', 'blog/show.blade.php', 'gallery/index.blade.php', 'gallery/show.blade.php', 'shop/index.blade.php', 'shop/show.blade.php', 'shop/cart.blade.php', 'shop/checkout.blade.php', 'shop/order_success.blade.php', 'shop/blocks/products.blade.php'] as $file) {
        expect(is_file($this->themePath.'/'.$file))->toBeTrue($file);
    }
});

test('owned views never borrow the layout or partials of another public theme', function (): void {
    foreach (['layouts', 'partials', 'pages', 'blog', 'gallery', 'shop'] as $directory) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->themePath.'/'.$directory, FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            preg_match_all('/theme-([a-z0-9-]+)::/', (string) file_get_contents($file->getPathname()), $matches);
            foreach (array_unique($matches[1]) as $id) {
                expect($id)->toBe($this->themeId, $file->getFilename());
            }
        }
    }
});

test('the owned layout exposes privacy accessibility overlays and editorial hooks', function (): void {
    $layout = file_get_contents($this->themePath.'/layouts/app.blade.php');
    foreach (["@yield('content')", "@stack('scripts')", "@stack('admin_scripts')", 'components.frontoffice-admin-bar', 'themes::components.cookie-consent-modal', 'themes::components.visitor-geo-modal', 'themes::components.scroll-magic', 'components.cms.overlays'] as $hook) {
        expect($layout)->toContain($hook);
    }
});
