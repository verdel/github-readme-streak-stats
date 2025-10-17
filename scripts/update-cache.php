<?php

require_once __DIR__. '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/cache.php';
require_once __DIR__ . '/../src/stats.php';

// load .env
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__. '/..', 1));
$dotenv->safeLoad();

foreach (array_filter(explode(',', $_SERVER["GIT_USER_WHITELIST"] ?? '')) as $username) {
    if (empty($username)) continue;
    getContributionGraphs($username);
    sleep(1);
}
