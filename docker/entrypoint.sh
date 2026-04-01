#!/usr/bin/env sh
set -e

if [ -f /var/www/html/.env ] && ! grep -q '^APP_KEY=base64:' /var/www/html/.env 2>/dev/null; then
    php artisan key:generate --force --no-interaction >/dev/null 2>&1 || true
fi

if [ ! -f /var/www/html/storage/app/installed.lock ] && [ -f /var/www/html/.env ] && grep -q '^DB_CONNECTION=sqlite' /var/www/html/.env 2>/dev/null; then
    sed -i 's/^DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' /var/www/html/.env
fi

if [ "${SKIP_DB_WAIT:-0}" = "1" ]; then
    exec "$@"
fi

if [ -n "${DB_HOST:-}" ]; then
    DB_PORT="${DB_PORT:-3306}"
    echo "Waiting for database at ${DB_HOST}:${DB_PORT}..."

    until php -r '
        $host = getenv("DB_HOST");
        $port = (int) (getenv("DB_PORT") ?: 3306);
        $timeout = 2;
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($socket) {
            fclose($socket);
            exit(0);
        }
        fwrite(STDERR, "waiting for db: {$errstr}\n");
        exit(1);
    '; do
        sleep 2
    done
fi

exec "$@"
