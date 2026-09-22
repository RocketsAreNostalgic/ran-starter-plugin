#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."
status=0
composer standards:fix || status=$?
# PHPCBF: 0 means clean; 1 means all fixable violations were corrected.
case "$status" in
    0|1) composer standards ;;
    *) exit "$status" ;;
esac
