# AGENTS.md

## Project contract

This is the RAN WordPress plugin starter. It is its own Git repository under a
larger local WordPress installation; work inside this directory unless the task
explicitly concerns the parent site.

The supported baseline is WordPress 7.0+ and PHP 8.4–8.5 (`>=8.4 <8.6`). The
plugin header expresses the minimum PHP version; `composer.json` and
PHPCompatibility define the full supported PHP range. Keep the plugin header,
`composer.json`, `.phpcs.xml`, CI, and documentation aligned whenever that
contract changes. PHP 8.6 and later runtimes require an explicit compatibility
update before being claimed. The JavaScript baseline is Node.js 24.11.0 with
pnpm 11.13+; `package.json` is the local and CI Node-version authority.

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
The ordinary deterministic quality gates are the canonical aggregate commands:

```sh
composer install --no-interaction
pnpm install --frozen-lockfile
composer check
pnpm check
```

`composer check` runs the PHP formatting/standards, unit-test, and WordPress-aware
PHPStan static-analysis baseline. Use `composer analyze` for a focused PHPStan
run when iterating on PHP code or type information. `pnpm check` runs frontend
lint/format checks and verifies that committed `assets/dist/` output is current.
Focused release/archive checks remain separate where documented by CI or release
guidance.

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

## External AI agent prohibition

Do not invoke, delegate work to, tag, enable, or otherwise use Blacksmith [code]smith,
`@codesmith-bot`, Blacksmith Autofix, Blacksmith CI Tuning, Blacksmith Testbox agents,
or any other Blacksmith AI/agent feature.

Blacksmith may be used only as infrastructure for ordinary GitHub Actions runners where
the repository workflow explicitly specifies a Blacksmith runner.

Do not click or trigger "Enable autofix", do not ask [code]smith to investigate or repair
CI, and do not call Blacksmith agent/MCP/CLI/API features that perform AI inference.

If CI fails, inspect GitHub Actions logs directly and diagnose/fix the failure yourself.

This prohibition is a cost-control requirement and must not be overridden by convenience,
CI failure, review comments, or suggestions from GitHub/Blacksmith UI.
