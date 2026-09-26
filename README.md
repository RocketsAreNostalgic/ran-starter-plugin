# RAN Starter Plugin

A maintained starting point for private WordPress plugins at Rockets Are
Nostalgic. It demonstrates a conventional plugin entry point, PSR-4 PHP,
optional admin/public assets, quality gates, and release automation. It is not
intended to be activated unchanged on a production site.

## Baseline

- RAN quality profile: `wordpress-plugin`
- WordPress 7.0 or newer
- PHP 8.4–8.5
- Node.js 24 or newer
- pnpm 11.13 or newer
- Composer 2

The plugin header expresses the minimum PHP version, while `composer.json` and
PHPCompatibility define the currently supported PHP range (`>=8.4 <8.6`). Keep
the plugin header, Composer constraint, PHP_CodeSniffer configuration, CI, and
documentation aligned whenever that contract changes. PHP 8.6 and PHP 9 are
not claimed until they are deliberately added to the compatibility contract.

## Create a plugin from the starter

1. Copy the repository and rename its directory and main plugin file.
2. Replace `RAN Starter Plugin`, `ran-starter-plugin`, and
   `Ran\\StarterPlugin` with the new product name, slug, and namespace.
3. Update the plugin header, `composer.json`, `package.json`, `.phpcs.xml`,
   release configuration, and this README as one identity change. Keep support
   ranges, prefixes/namespaces, source paths, globals and justified exceptions
   local to the derived repository.
4. Retain the shared `ran/coding-standards` and
   `@rocketsarenostalgic/quality-config` ancestry. Do not copy their shared
   rules back into local configuration. Keep the `wordpress-plugin` profile
   unless the derived repository's actual code surface requires a reviewed
   profile change.
5. Regenerate both tracked lockfiles from the changed manifests and review the
   resulting dependency graph. Do not replace locked shared-package revisions
   with floating, unreviewed branch state.
6. Remove unused example controllers, templates, and assets before adding new
   product behaviour.
7. Decide whether the derived plugin is private/internal or publicly supported.
   Public RAN plugins must conform to the
   [RAN Community Standards](https://github.com/RocketsAreNostalgic/.github/blob/main/COMMUNITY_STANDARDS.md)
   before public support or contribution intake is enabled.
8. Run the checks below before the first commit and keep any stronger
   project-specific gates that the derived plugin introduces.

## Install and verify

```sh
composer install
pnpm install --frozen-lockfile
composer check
pnpm check
```

`composer check` is the ordinary deterministic PHP quality contract. It includes
formatting, the shared `RANWordPressPlugin` PHPCS/WPCS/PHPCompatibility
baseline plus Starter-local rules, unit tests, and WordPress-aware PHPStan
static analysis. Use `composer analyze` when you want to run PHPStan by itself.
`pnpm check` is the ordinary deterministic frontend contract: ESLint, Prettier
and Stylelint derive from `@rocketsarenostalgic/quality-config`, Starter-local
rules remain local, and committed asset freshness is verified.

`ran/coding-standards` uses the compatible stable constraint `^1.0`;
`composer.lock` currently pins v1.0.0 at
`6af816a02b7d1108ad5c990e9d0fda0af0a13de7`. PHPCS and PHPCBF share the
existing project rules; `RANOwnedMethods` remains opt-in and is not enabled here.
The frontend package remains on its reviewed Phase 7 candidate:
`pnpm-lock.yaml` binds `@rocketsarenostalgic/quality-config` to
`751edd097e3902efb93992bf47401a1a4f4b1fa8`. Its released-version adoption is a
separate reviewed dependency update.

Composer resolves the RAN plugin library and coding-standard package from
their declared GitHub VCS sources; `composer.lock` is tracked to make those
resolutions reproducible. A deployable archive must include `vendor/`; a source
checkout needs `composer install`. Do not delete the lockfile or replace
`composer install` with `composer update` in setup scripts.

### Assets

Source assets are under `assets/src/`; compiled runtime assets are committed in
`assets/dist/`.

```sh
pnpm build
pnpm check
```

`check:generated`, which is included by `pnpm check`, rebuilds the bundle and
fails if `assets/dist/` is stale. The asset build path uses `pnpm check:source`
before rebuilding so legitimate source changes can update stale generated
output. The pre-commit hook applies the same generation rule when a staged
change can affect generated assets. It intentionally does not rebuild for
documentation, release, lint, or test-script-only edits.

## Commits

Use Conventional Commits. Keep one coherent change per commit and use an
imperative, specific subject:

```text
feat: add the newsletter subscription endpoint
fix: preserve the selected image focal point
chore: update the PHP quality tooling
docs: clarify the local development setup
```

`feat:` creates a minor-version release candidate and `fix:` creates a patch
release candidate. Use `chore:`, `docs:`, `test:`, `build:`, or `ci:` for work
that should not independently trigger a plugin release. Use `!` or a
`BREAKING CHANGE:` footer only for a deliberate breaking public contract.

## Agent workflow

See [AGENTS.md](AGENTS.md) for the repository operating guide, Dex-backed
working plans and execution records, the installed official WordPress agent
skills, the `wordpress-plugin` RAN quality profile and shared-standard
boundaries, and the community-health requirements for derived plugins.

## Community health

Public repositories owned by Rockets Are Nostalgic inherit the organization's
default community-health files when they do not provide a local equivalent.
Public, supported RAN-derived WordPress plugins must either inherit that complete
baseline or provide local files that preserve the same conduct, disclosure,
privacy, security-reporting, issue-intake, and pull-request safeguards. Private
and internal derivatives do not inherit the organization defaults and must
provide local equivalents when they rely on those policies.

Local community files should be added only when a plugin needs project-specific
support scope, supported-version statements, diagnostics, or contribution
checks. Local `CONTRIBUTING.md` and pull-request guidance must use the actual
validation contract for that derived repository rather than copying commands
from another plugin.

If any local issue template or issue-template configuration is introduced, the
derived repository must provide its complete local intake set because GitHub
suppresses the organization default issue templates rather than combining the
two sets.

## Release Please

`Quality` qualifies both pull-request heads and trusted pushes to `main`. The
local Release Please workflow is now a thin caller of the organisation-owned
Profile A release contract in `RocketsAreNostalgic/.github`, pinned to an exact
approved revision. The shared contract admits only the actual successful
same-repository `Quality` `workflow_run` for a push to `main`, proves the exact
admitted revision in a read-only job, and grants Release Please write authority
only after that admission. Immediately before mutation it also requires current
`main` still to equal the admitted revision and explicitly targets Release
Please at `main`.

Release Please remains authoritative for version calculation, changelog,
release-PR lifecycle, tags and GitHub Releases. Because Release Please mutations
made with `GITHUB_TOKEN` do not recursively trigger pull-request workflows, the
shared Profile A contract also binds the configured bot-owned release-PR branch
to its exact head and dispatches this repository's existing read-only `Quality`
workflow only when no successful or in-flight qualification already covers that
head. The repository does not maintain a second release publisher, version
engine, lifecycle-label reconciler, historical recovery path, or separate
post-publication state machine.

Release Please opens or updates a release PR from Conventional Commits. Merging
that PR creates the version tag and GitHub release; it does not publish to
WordPress.org or deploy a site.

The release manifest is currently at `0.1.0` and keeps the main plugin header and
`package.json` version synchronized. Before merging a release PR, verify its
proposed version, changelog, generated assets, and the distributable archive
separately:

```sh
pnpm release:archive:check
pnpm release:archive -- --output=/tmp/ran-starter-plugin.zip
```

The archive builder copies only the explicit runtime allowlist in
`release-contents.txt`, installs production Composer dependencies in a
temporary staging directory, normalizes archive metadata, and validates the
resulting ZIP. A derived plugin must review that allowlist and validation
contract along with its renamed identity before relying on it for a release.

Do not manually edit a generated release version or tag. Correct the Release
Please configuration instead, then let the next release PR make the change.

## WordPress.org publication

The starter is private by default and is not submission-ready on its own. A
derived public plugin needs its own `readme.txt`, translation contract,
clean-install/Plugin Check coverage, directory assets, and SVN handoff. It may
start from this archive builder, but must make the allowlist and validation
product-specific. See [PRE-RELEASE-CHECKLIST.md](PRE-RELEASE-CHECKLIST.md) for
the operator checklist and [RELEASE.md](RELEASE.md) for the full, deliberately
separate WordPress.org publication path.

## Scope and support

This is a private RAN development starter. It has no present WordPress.org
publishing or public support commitment. Individual plugins own their runtime
behaviour; this repository owns the reusable development and release
conventions.

## Native PHP qualification

Quality runs the locked PHP checks and project archive/install tests on both
PHP 8.4 and 8.5. Each project lane verifies its actual PHP interpreter and
installed dependency platform requirements before installing and activating
the built archive on WordPress 7.0. The terminal `quality` check requires both
versions to succeed. The tracked dependency lock remains unchanged; installed
dependency checks verify the real runtime rather than relying on support
declarations alone.
