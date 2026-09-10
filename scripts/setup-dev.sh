#!/bin/sh

set -eu

composer install --no-interaction
pnpm install --frozen-lockfile

printf '%s\n' 'Development dependencies installed.'
printf '%s\n' 'Run composer check and pnpm check before committing.'
