<?php

declare(strict_types=1);

namespace Tests\Support;

use DbCms\Core\Blocks\BlockPackageManager;
use DbCms\Core\Contracts\Services\GeneralSettingsServiceInterface;
use DbCms\Core\Http\Middleware\EnsureCmsIsInstalled;
use DbCms\Core\Http\Middleware\RedirectIfInstalled;
use DbCms\Core\Models\CmsModule;
use DbCms\Core\Modules\ModuleManager;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends LaravelTestCase
{
    /** @var list<string>|null */
    private static ?array $migratedModules = null;

    /** @return array<string, mixed> */
    private function composition(): array
    {
        $composition = require dirname(__DIR__).'/package.php';
        $profile = getenv('DBCMS_TEST_PROFILE') ?: 'default';
        if ($profile !== 'default') {
            $composition = array_replace($composition, $composition['profiles'][$profile]);
        }
        $composition['modules'] = $composition['defaultTestModules'] ?? $composition['modules'];
        if (defined(static::class.'::TEST_MODULES')) {
            $composition['modules'] = static::TEST_MODULES;
        }
        if (defined(static::class.'::TEST_BLOCK_PACKAGES')) {
            $composition['blockPackages'] = static::TEST_BLOCK_PACKAGES;
        }

        return $composition;
    }

    public function createApplication(): Application
    {
        $selection = implode(',', $this->composition()['modules']);
        putenv('CMS_MODULES='.$selection);
        $_ENV['CMS_MODULES'] = $_SERVER['CMS_MODULES'] = $selection;
        $app = require dirname(__DIR__, 2).'/.build/app/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /** @return array<class-string, class-string> */
    protected function setUpTraits(): array
    {
        $this->withoutMiddleware([EnsureCmsIsInstalled::class, RedirectIfInstalled::class]);
        Storage::fake('uploads');
        $composition = $this->composition();
        $refreshesDatabase = in_array(RefreshDatabase::class, class_uses_recursive(static::class), true);
        if ($refreshesDatabase) {
            if (self::$migratedModules !== $composition['modules']) {
                RefreshDatabaseState::$migrated = false;
            }
            self::$migratedModules = $composition['modules'];
        }
        $traits = parent::setUpTraits();
        if (! $refreshesDatabase) {
            return $traits;
        }
        // Providers load migration paths before RefreshDatabase opens its transaction.
        // Fixture availability changes no schema. Real installation is tested by test:dist.
        app(ModuleManager::class)->reconcileModules($composition['modules']);
        CmsModule::query()->whereIn('identifier', $composition['modules'])
            ->update(['is_installed' => true, 'is_active' => true]);
        foreach ($composition['blockPackages'] as $identifier) {
            app(BlockPackageManager::class)->setEnabled($identifier, true);
        }
        if ($composition['privacyProcessing'] !== []) {
            app(GeneralSettingsServiceInterface::class)->set('legal.processing', $composition['privacyProcessing'], 'legal', 'json');
        }

        return $traits;
    }
}
