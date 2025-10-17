#!/bin/sh
set -e

for var in TOKEN GIT_USER_WHITELIST; do
    eval value=\$$var
    [ -n "$value" ] && echo "${var}=$value" >> .env
done

php /app/scripts/update-cache.php > /dev/null 2>&1 || echo "Update cache on startup failed"

crond -L /dev/null

exec php -S 0.0.0.0:8000 -t src || {
    echo "Failed to start PHP server"
    exit 1
}
