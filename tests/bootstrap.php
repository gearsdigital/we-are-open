<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Let PHPUnit own error handling, including for cloned Kirby instances.
Kirby\Cms\App::$enableWhoops = false;

new Kirby\Cms\App([
    'roots' => [
        'index'   => dirname(__DIR__),
        'content' => __DIR__ . '/fixtures/content',
    ],
]);
