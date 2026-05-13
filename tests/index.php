<?php

if (PHP_SAPI === 'cli') {
    error_reporting(error_reporting() & ~E_DEPRECATED);
}

require_once __DIR__.'/../vendor/autoload.php';

if (PHP_SAPI !== 'cli') {
    $kirby = new Kirby;

    echo $kirby->render();
}
