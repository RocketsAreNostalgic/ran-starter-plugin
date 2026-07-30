# Pre-release checklist

Use this for a derived plugin that has completed the one-time WordPress.org
publication enablement in [RELEASE.md](RELEASE.md). The starter itself is
private and does not satisfy these checks until the product-specific items are
implemented.

## Product and directory readiness

- [ ] The public name, approved directory slug, text domain, contributor
      account, support owner, and public links are final.
- [ ] The product is complete and production-ready, not a scaffold or
      placeholder, and has a meaningful reason to be in the directory.
- [ ] Licences, copyrights, trademarks, third-party services, and directory
      artwork have been reviewed; every shipped asset and dependency is
      GPL-compatible.
- [ ] The main header, `package.json`, Release Please manifest, `readme.txt`
      stable tag, and intended SVN tag agree on the same version where each
      source applies.
- [ ] `readme.txt` has been checked with the current WordPress.org readme
      validator and accurately documents installation, privacy/external
      services, support, compatibility, changelog, and screenshots.
- [ ] User-facing strings use the final text domain; the POT template is
      regenerated and reviewed if the product ships translatable strings.

## Release artifact and verification

- [ ] Worktree is clean and all committed generated runtime assets are current.
- [ ] Run the normal starter gate and archive validation shown below.

```sh
composer install --no-interaction
pnpm install --frozen-lockfile
pnpm check
pnpm check:generated
composer test
composer run cs:check
composer run standards
pnpm release:archive:check
```

- [ ] Run the product's adapted archive build, archive verification,
      integration, and Plugin Check commands.
- [ ] Inspect the ZIP: one `<slug>/` root, correct main plugin file and
      version, runtime `vendor/` and built assets when required, no credentials,
      logs, test files, caches, source-only tooling, or directory artwork.
- [ ] Install and activate the ZIP on a clean WordPress installation and
      exercise the product's primary feature and failure states.
- [ ] Review the generated Release Please PR, then merge it only after the
      gate passes. Rebuild and verify the artifact from the resulting Git tag.

## WordPress.org hand-off

- [ ] For first submission, upload the complete verified ZIP with an accurate
      overview and wait for approval before expecting SVN access.
- [ ] For an approved plugin, deploy only the validated archive contents to SVN
      `trunk`, commit, then copy that exact trunk state to `tags/<version>`.
- [ ] Upload approved banners, icons, and screenshots separately to SVN
      `/assets`; keep them out of the release ZIP unless they are runtime files.
- [ ] Confirm the public directory page and update path resolve to the intended
      version, and keep support/security contact arrangements active.
