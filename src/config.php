<?php

// Cache settings
define('CACHE_DIR', '/var/cache/github-stats');
define('CACHE_TTL', 3600); // 1 hour

// Create cache directory if it doesn't exist
if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}
