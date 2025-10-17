<?php

require_once __DIR__ . '/config.php';

class Cache {
    private static array $GIT_USER_WHITELIST = [];
    private static bool $initialized = false;

    /**
     * Проверяем инициализацию, если нужно — загружаем whitelist из окружения
     */
    private static function ensureInitialized(): void {
        if (!self::$initialized) {
            self::$GIT_USER_WHITELIST = array_filter(
                explode(',', $_SERVER["GIT_USER_WHITELIST"] ?? '')
            );
            self::$initialized = true;
        }
    }

    public static function isWhitelisted(string $username): bool {
        self::ensureInitialized(); // гарантирует, что whitelist уже загружен
        return empty(self::$GIT_USER_WHITELIST) || in_array($username, self::$GIT_USER_WHITELIST);
    }

    public static function get(string $username): ?array {
        self::ensureInitialized();
        $cacheFile = self::getCacheFile($username);
        if (!file_exists($cacheFile)) {
            return null;
        }

        $cacheData = json_decode(file_get_contents($cacheFile));
        if (!$cacheData || (time() - $cacheData->timestamp) > CACHE_TTL) {
            return null;
        }

        return (array) $cacheData->data;
    }

    public static function set(string $username, array $data): void {
        self::ensureInitialized();
        $cacheFile = self::getCacheFile($username);
        $cacheData = [
            'timestamp' => time(),
            'data' => $data
        ];
        file_put_contents($cacheFile, json_encode($cacheData));
    }

    private static function getCacheFile(string $username): string {
        return CACHE_DIR . '/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $username) . '.json';
    }
}
