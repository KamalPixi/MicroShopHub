#!/usr/bin/env sh
set -e

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
