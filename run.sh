#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

if [ ! -f .env ]; then
    cp .env.example .env
fi

# A stale hot file (left by a stopped Vite dev server) makes the page load assets from a dead server.
# This can happen when the script is run twice, e.g. after `npm run dev` was used in between.
rm -f public/hot

# Sail lives in vendor/, so dependencies must be installed before it can be used.
# The throwaway Composer image may run another PHP version than Sail, hence --ignore-platform-reqs.
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    composer:2 \
    composer install --ignore-platform-reqs --no-interaction

./vendor/bin/sail up -d --build --wait

if ! grep -qE '^APP_KEY=.+' .env; then
    ./vendor/bin/sail artisan key:generate
fi

./vendor/bin/sail artisan migrate --no-interaction
./vendor/bin/sail artisan db:seed --no-interaction

./vendor/bin/sail npm install
./vendor/bin/sail npm run build
