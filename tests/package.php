<?php

declare(strict_types=1);

return [
    'laravel' => ['version' => '13.11.0', 'reference' => '06d016a364a37430eec9cbc52209adce3d7667ce'],
    'kind' => 'theme',
    'core' => '0.0.1',
    'fixtureVersion' => '0.0.1',
    'theme' => 'db-minimal',
    'adminTheme' => 'dbcms',
    'require' => [],
    'sourceDirs' => [],
    'testPaths' => ['Architecture', 'Feature'],
    'applicationTestPaths' => ['Architecture', 'Feature', 'Integration'],
    'modules' => [],
    'blockPackages' => [],
    'blockTypes' => [],
    'privacyProcessing' => [],
    'preserveRows' => [],
    'profiles' => [
        'content' => [
            'require' => ['dbravoan/dbcms-module-blog' => '0.0.1', 'dbravoan/dbcms-module-gallery' => '0.0.1'],
            'modules' => ['blog', 'gallery'],
            'testPaths' => ['Integration/ContentThemeTest.php'],
        ],
        'popups' => [
            'require' => ['dbravoan/dbcms-module-popups' => '0.0.1'],
            'modules' => ['popups'],
            'testPaths' => ['Integration/PopupsThemeTest.php'],
        ],
    ],
];
