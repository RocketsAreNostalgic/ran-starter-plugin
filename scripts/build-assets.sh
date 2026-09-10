#!/bin/sh

set -eu

pnpm check:source
pnpm build
