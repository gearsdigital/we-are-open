<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

new Kirby\Cms\App([
    'roots' => [
        'index'   => __DIR__,
        'content' => __DIR__ . '/fixtures/content',
    ],
]);
