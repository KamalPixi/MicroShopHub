#!/usr/bin/env sh
set -e

if [ ! -f /var/www/html/.env ] && [ -f /var/www/html/.env.example ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

if [ -f /var/www/html/.env ] && ! grep -q '^APP_KEY=base64:' /var/www/html/.env 2>/dev/null; then
    APP_KEY_VALUE="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"
    if grep -q '^APP_KEY=' /var/www/html/.env 2>/dev/null; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY_VALUE}|" /var/www/html/.env
    else
        printf '\nAPP_KEY=%s\n' "$APP_KEY_VALUE" >> /var/www/html/.env
    fi
fi

if [ ! -f /var/www/html/storage/app/installed.lock ] && [ -f /var/www/html/.env ] && grep -q '^DB_CONNECTION=sqlite' /var/www/html/.env 2>/dev/null; then
    sed -i 's/^DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' /var/www/html/.env
fi

if [ ! -f /var/www/html/storage/app/installed.lock ] && [ -f /var/www/html/bootstrap/cache/config.php ]; then
    rm -f /var/www/html/bootstrap/cache/config.php
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
