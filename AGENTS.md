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

## Community health

Public RAN-derived plugins must conform to the
[RAN Community Standards](https://github.com/RocketsAreNostalgic/.github/blob/main/COMMUNITY_STANDARDS.md).
Public repositories in the RAN organization inherit the default community-health
files from the public organization `.github` repository when they do not define
a local equivalent. Private and internal derivatives do not inherit those
defaults and must provide local equivalents when they rely on those policies.

A derived plugin may add local `CODE_OF_CONDUCT.md`, `CONTRIBUTING.md`,
`SUPPORT.md`, `SECURITY.md`, issue forms, or a pull-request template when its
project contract requires more specific guidance. Local overrides must preserve
the organization privacy, disclosure, conduct, and security-reporting
boundaries. In particular, public issues and pull requests must not contain
private repository/site identities, secrets, private source, customer data, or
vulnerability details.

GitHub does not merge a repository's local issue-template set with the
organization defaults. If a derived plugin adds any local issue template or
`.github/ISSUE_TEMPLATE/config.yml`, it must provide the complete local intake
surface it needs and preserve all required safety acknowledgements.

A public derived plugin outside the RAN organization does not receive the
organization defaults automatically; copy or recreate compliant community
health files before offering public support or contributions.

## Development workflow

Install from the tracked locks; never use a setup script that deletes them.

```sh
composer install --no-interaction
pnpm install --frozen-lockfile
pnpm check
pnpm check:generated
composer test
composer run standards -- --report=summary
```

Source assets live in `assets/src/`; compiled runtime files in `assets/dist/`
are committed. When a staged change can affect generated assets, the
pre-commit hook rebuilds and requires the resulting `assets/dist/` changes to
be staged. Tooling, documentation, and release-only changes must not trigger
an unnecessary rebuild.

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
