#!/usr/bin/env bash
set -euo pipefail

repo_root=$(CDPATH='' cd -- "$(dirname -- "$0")/.." && pwd)
quality="$repo_root/.github/workflows/quality.yml"
release="$repo_root/.github/workflows/release-please.yml"

require() {
	local file=$1
	local text=$2
	grep -Fq -- "$text" "$file" || {
		printf 'Missing release workflow contract in %s: %s\n' "$file" "$text" >&2
		exit 1
	}
}

reject() {
	local file=$1
	local text=$2
	if grep -Fq -- "$text" "$file"; then
		printf 'Forbidden release workflow contract in %s: %s\n' "$file" "$text" >&2
		exit 1
	fi
}

require "$quality" 'push:'
require "$quality" '- main'
require "$quality" 'workflow_dispatch:'
require "$quality" "github.event_name == 'pull_request' && github.event.pull_request.head.sha || github.sha"
require "$quality" 'cancel-in-progress: ${{ github.event_name == '\''pull_request'\'' }}'
require "$quality" 'bash tests/release-workflow-contract.sh'

require "$release" 'workflow_run:'
require "$release" 'workflows: [Quality]'
require "$release" 'types: [completed]'
require "$release" 'branches: [main]'
require "$release" 'permissions: {}'
require "$release" 'actions: write'
require "$release" 'contents: write'
require "$release" 'issues: write'
require "$release" 'pull-requests: write'
require "$release" "github.event.workflow_run.event == 'push'"
require "$release" "github.event.workflow_run.conclusion == 'success'"
require "$release" "github.event.workflow_run.head_branch == 'main'"
require "$release" 'github.event.workflow_run.head_repository.id == github.repository_id'
require "$release" 'github.event.workflow_run.head_repository.full_name == github.repository'
require "$release" '.path == ".github/workflows/quality.yml"'
require "$release" '.event == "push"'
require "$release" '.conclusion == "success"'
require "$release" '.head_sha == $commit'
require "$release" 'ref: ${{ steps.quality.outputs.commit }}'
require "$release" 'persist-credentials: false'
require "$release" 'test "$(git rev-parse HEAD)" = "$RAN_QUALITY_COMMIT"'
require "$release" 'googleapis/release-please-action@45996ed1f6d02564a971a2fa1b5860e934307cf7'
reject "$release" 'googleapis/release-please-action@v5'
require "$release" 'Dispatch exact Release Please candidate Quality'
require "$release" "expected_head='release-please--branches--main--components--ran-starter-plugin'"
require "$release" '.user.login == $bot'
require "$release" '.head.repo.full_name == $repository'
require "$release" 'actions/workflows/quality.yml/dispatches'
require "$release" "'{ref: $ref}'"
require "$release" 'if: steps.release.outputs.release_created == '\''true'\'''
require "$release" 'test "$RAN_RELEASE_SHA" = "$RAN_QUALITY_COMMIT"'
require "$release" 'git/ref/tags/${RAN_TAG_NAME}'
require "$release" '.tag_name == $tag'
require "$release" '.target_commitish == $commit'
require "$release" '.draft == false'
require "$release" '(.assets | length) == 0'

printf 'Release workflow contract tests passed.\n'
