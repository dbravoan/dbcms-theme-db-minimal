<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$app = $root.'/.build/app';
require $app.'/vendor/autoload.php';

spl_autoload_register(static function (string $class) use ($root): void {
    if (str_starts_with($class, 'Tests\\')) {
        $file = $root.'/tests/'.str_replace('\\', '/', substr($class, 6)).'.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});

$composition = require $root.'/tests/package.php';
$profile = getenv('DBCMS_TEST_PROFILE') ?: 'default';
if ($profile !== 'default') {
    $composition = array_replace($composition, $composition['profiles'][$profile]);
}
$selection = implode(',', $composition['modules']);
putenv('CMS_MODULES='.$selection);
$_ENV['CMS_MODULES'] = $_SERVER['CMS_MODULES'] = $selection;

foreach ($composition['bootstrapFiles'] ?? [] as $file) {
    require_once $root.'/'.$file;
}
