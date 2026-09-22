#!/usr/bin/env bash
set -euo pipefail

repo_root=$(CDPATH='' cd -- "$(dirname -- "$0")/.." && pwd)
quality="$repo_root/.github/workflows/quality.yml"
release="$repo_root/.github/workflows/release-please.yml"

require() {
	local file=$1 text=$2
	grep -Fq -- "$text" "$file" || {
		printf 'Missing release workflow contract in %s: %s\n' "$file" "$text" >&2
		exit 1
	}
}
reject() {
	local file=$1 text=$2
	if grep -Fq -- "$text" "$file"; then
		printf 'Forbidden release workflow contract in %s: %s\n' "$file" "$text" >&2
		exit 1
	fi
}

require "$quality" 'push:'
require "$quality" '- main'
require "$quality" 'workflow_dispatch:'
require "$quality" "github.event_name == 'pull_request' && github.event.pull_request.head.sha || github.sha"
require "$quality" 'bash tests/release-workflow-contract.sh'

require "$release" 'workflow_run:'
require "$release" 'workflows: [Quality]'
require "$release" 'types: [completed]'
require "$release" 'branches: [main]'
require "$release" 'permissions: {}'
require "$release" 'contents: write'
require "$release" 'issues: write'
require "$release" 'pull-requests: write'
require "$release" 'uses: RocketsAreNostalgic/.github/.github/workflows/release-profile-a.yml@d758380ba2c1eefd2196ca66028661e189db56e0'
require "$release" 'expected-workflow-path: .github/workflows/quality.yml'

reject "$release" 'gh api'
reject "$release" 'release-please-action'
reject "$release" 'autorelease:'
reject "$release" 'RAN_RELEASE'
reject "$release" 'actions: write'
reject "$release" 'secrets: inherit'

printf 'Release workflow contract tests passed.\n'
