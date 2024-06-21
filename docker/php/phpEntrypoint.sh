#!/usr/bin/env sh

set -e

printf "\n\nStarting PHP 8.0 daemon...\n\n"

# php artisan migrate && php artisan storage:link

exec "$@"