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

During the Phase 7 proof, `composer.lock` binds `ran/coding-standards` to exact
candidate `0b03e61a4bb558deeb6bc6b6399f44c0ec95e5be`, while `pnpm-lock.yaml`
binds `@rocketsarenostalgic/quality-config` to exact candidate
`751edd097e3902efb93992bf47401a1a4f4b1fa8`. Once both Starter and Booster
prove those candidates and versioned shared-package releases exist, this
repository should move to the released versions through a reviewed dependency
update rather than silently following a mutable branch.

Composer resolves the RAN plugin library and coding-standard candidate from
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

Release Please watches pushes to `main` and opens or updates a release PR from
Conventional Commits. Merging that PR creates the version tag and GitHub
release; it does not publish to WordPress.org or deploy a site.

The release manifest starts at the current `0.0.4` plugin version and keeps the
main plugin header and `package.json` version synchronized. Before merging the
first release PR, verify its proposed version, changelog, generated assets, and
the distributable archive separately:

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
