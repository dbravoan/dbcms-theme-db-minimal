<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\TestCase;

$composition = require __DIR__.'/package.php';
$profile = getenv('DBCMS_TEST_PROFILE') ?: 'default';
if ($profile !== 'default') {
    $composition = array_replace($composition, $composition['profiles'][$profile]);
}
uses(TestCase::class, RefreshDatabase::class)->in(...($composition['applicationTestPaths'] ?? $composition['testPaths']));
