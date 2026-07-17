#!/bin/sh

set -eu

composer install --no-interaction
pnpm install --frozen-lockfile

printf '%s\n' 'Development dependencies installed.'
printf '%s\n' 'Run pnpm check, pnpm check:generated, and composer test before committing.'
