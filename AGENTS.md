# AGENTS.md

## Project contract

This is the RAN WordPress plugin starter. It is its own Git repository under a
larger local WordPress installation; work inside this directory unless the task
explicitly concerns the parent site.

The supported baseline is WordPress 7.0+ and PHP 8.4+. Keep the plugin header,
`composer.json`, `.phpcs.xml`, CI, and documentation aligned whenever that
contract changes. The JavaScript baseline is Node.js 24.11.0 with pnpm 11.13+;
`package.json` is the local and CI Node-version authority.

## Dex: plans and execution record

Use Dex for non-trivial plans and implementation work. Its local state is
intentionally private and ignored by Git.

```sh
dex --storage-path .dex status
dex --storage-path .dex create "Short outcome" --description "Scope, acceptance criteria, and checks"
dex --storage-path .dex start <id>
dex --storage-path .dex complete <id> --result "What changed and how it was verified" --commit <sha>
```

- Use one parent task per meaningful outcome and child tasks for independently
  verifiable slices.
- Record decisions, validation, and follow-up work in the Dex task result.
- Do not commit, copy, delete, or externally sync `.dex` without the user's
  explicit direction.
- Keep durable project decisions in tracked Markdown documentation; Dex is the
  working plan and execution ledger, not published project history.

## WordPress skills

The official WordPress project skills are installed under `.codex/skills/`.
Read the relevant `SKILL.md` before working in its area:

- `wordpress-router` and `wp-project-triage` for initial orientation.
- `wp-plugin-development` for plugin structure, hooks, settings, security, and
  WordPress conventions.
- `wp-wpcli-and-ops` for WP-CLI or operational changes.
- `wp-phpstan` when adding or changing static analysis.

## Development workflow

Install from the tracked locks; never use a setup script that deletes them.
The ordinary deterministic quality gates are the canonical aggregate commands:

```sh
composer install --no-interaction
pnpm install --frozen-lockfile
composer check
pnpm check
```

`composer check` runs the PHP formatting/standards and unit-test baseline.
`pnpm check` runs frontend lint/format checks and verifies that committed
`assets/dist/` output is current. Focused release/archive checks remain separate
where documented by CI or release guidance.

Source assets live in `assets/src/`; compiled runtime files in `assets/dist/`
are committed. When a staged change can affect generated assets, the
pre-commit hook rebuilds and requires the resulting `assets/dist/` changes to
be staged. Tooling, documentation, and release-only changes must not trigger
an unnecessary rebuild. The asset build path runs `pnpm check:source` before
building so a legitimate stale `assets/dist/` tree can be regenerated; use
`pnpm check` after generation when verifying parity.

## Git and releases

Use Conventional Commits with one coherent change per commit. `feat:` and
`fix:` are releasable; use `chore:`, `docs:`, `test:`, `build:`, or `ci:` for
non-release work. See `README.md` for examples and the Release Please lifecycle.

Release Please manages versions from `main`. Do not manually alter generated
release versions, tags, or changelog sections. A release is not a WordPress.org
publication or a deployment; those require separate explicit authorization.
For a derived product intended for WordPress.org, follow `RELEASE.md`; it
defines the publication-enablement work and release/SVN hand-off separately
from the GitHub release lifecycle.

Never commit `vendor/`, `node_modules/`, `.dex`, test caches, editor-local
files, or generated artifacts that are not part of `assets/dist/`.
