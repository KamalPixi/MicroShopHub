#!/usr/bin/env sh
set -eu

COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.nerdctl.yml}"

echo "Starting database..."
nerdctl compose -f "${COMPOSE_FILE}" up -d db

echo "Waiting for database readiness..."
until nerdctl compose -f "${COMPOSE_FILE}" exec -T db sh -lc 'mysqladmin ping -uroot -p"${MYSQL_ROOT_PASSWORD:-rootsecret}" --silent' >/dev/null 2>&1; do
    sleep 2
done

echo "Running setup..."
nerdctl compose -f "${COMPOSE_FILE}" up setup

echo "Starting application services..."
nerdctl compose -f "${COMPOSE_FILE}" up app nginx queue scheduler phpmyadmin

echo "Done."
