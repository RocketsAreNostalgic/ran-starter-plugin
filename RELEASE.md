# Release and WordPress.org publication checklist

GitHub is the development source for a plugin created from this starter.
Release Please turns Conventional Commits into a versioned GitHub release; it
does not build a submission ZIP, submit a plugin for review, publish to
WordPress.org, or deploy a site.

This starter remains private by default. Use this checklist only after a
derived plugin has a deliberate public-product decision and an owner for
support, security, and releases.

## One-time publication enablement

Complete these items before the first directory submission. They are not
provided as generic files in the starter because they must describe the real
plugin rather than a placeholder.

- [ ] Finalise the public plugin name, directory slug, text domain, plugin URI,
      author/support details, and public repository links. The approved
      WordPress.org slug controls the directory URL, SVN path, installed folder,
      and text-domain identity; do not treat it as a temporary name.
- [ ] Confirm a meaningful, production-ready use case and a support owner. A
      starter or placeholder plugin is not suitable for submission.
- [ ] Review the main plugin header. Its version, `Requires at least`,
      `Requires PHP`, license, text domain, and `Tested up to` metadata must
      accurately describe the public product. Keep the header version aligned
      with `package.json`, the Release Please manifest, the directory stable
      tag, and the eventual SVN tag.
- [ ] Confirm every shipped file, including Composer dependencies, fonts,
      images, and generated assets, is GPL-compatible and has the required
      copyright, trademark, and third-party-service permission. GPL-2.0-or-
      later is the usual directory choice; any alternative must remain
      GPL-compatible and be documented consistently.
- [ ] Add a WordPress.org-compatible `readme.txt` for the real product. It must
      contain honest installation, FAQ, external-service/privacy, support,
      tested-version, changelog, and stable-tag information. Validate it with
      the current directory readme validator before submission.
- [ ] Make every user-facing PHP, JavaScript, and block-metadata string
      translatable with the final text domain. Add and maintain a POT template;
      do not ship unreviewed `.po` or `.mo` files merely to fill the archive.
- [ ] Add a product-specific, allowlisted archive builder and verification
      command. It must produce one `<slug>/` top-level directory, include the
      exact runtime files (including `vendor/` and committed `assets/dist/`
      when needed), and exclude source-only, test, cache, credential, and
      development files. Do not use a blanket Git export as the release ZIP.
- [ ] Add an archive-install smoke test and an official Plugin Check run against
      the built ZIP or its unpacked plugin directory. Test activation and the
      actual feature on a clean WordPress installation.
- [ ] Add `wordpress-org/assets/` only when approved public directory artwork
      exists. Keep licensed icons, banners, and screenshots outside the plugin
      ZIP, with a product-specific SVN hand-off document.

The RAN plugins in this workspace demonstrate the target shape: an explicit
archive allowlist, archive verification, fresh-install/Plugin Check coverage,
`readme.txt`, translation template, public artwork hand-off, and a manual SVN
runbook. Copy those contracts deliberately for the derived product; do not
copy their slug, product claims, services, or release commands wholesale.

## Release gate for every public version

Run this from a clean worktree after dependencies are installed. These are the
starter's current quality commands; a public product must extend the gate with
its own POT, archive, archive-verification, integration, and Plugin Check
commands from the one-time enablement section.

```sh
composer install --no-interaction
pnpm install --frozen-lockfile
pnpm check
pnpm check:generated
composer test
composer run cs:check
composer run standards
```

Before accepting a Release Please PR:

- [ ] Review the proposed semantic version, changelog, plugin header, and
      `package.json` version together.
- [ ] Run the full quality and public-product archive gates on the proposed
      release commit.
- [ ] Confirm generated runtime assets and translations are current and staged.
- [ ] Build the allowlisted ZIP, inspect its contents, install it into a fresh
      WordPress site, activate it, and exercise its primary feature.
- [ ] Run Plugin Check on that release artifact and resolve actionable results.
- [ ] Confirm the release ZIP is under WordPress.org's submission size limit
      and contains no logs, credentials, development tooling, or unnecessary
      dependencies.

Merge the reviewed Release Please PR only after that approval. It creates the
Git tag and GitHub release; it is still not a WordPress.org deployment. Build
and verify the final archive again from the resulting tag before handing it to
the directory.

## First directory submission

1. Register the submitter on WordPress.org using a monitored, organisation-
   appropriate email address, and ensure messages from `plugins@wordpress.org`
   can be received.
2. Submit the complete, production-ready, verified ZIP with an accurate short
   description. Do not submit a scaffold, placeholder, or development archive.
3. Wait for the review outcome and use the approved slug from the response. Do
   not create or assume an SVN repository before approval.
4. Address review feedback in the source repository, repeat the release gate,
   and submit an updated verified ZIP when requested.

## Directory publication after approval

Use WordPress.org SVN as a deployment repository, not as day-to-day source
control. Starting from the verified archive built from the Git tag:

```sh
svn checkout https://plugins.svn.wordpress.org/<approved-slug>/ <slug>-svn
unzip -q dist/<slug>-<version>.zip -d /tmp/<slug>-release
rsync -a --delete /tmp/<slug>-release/<slug>/ <slug>-svn/trunk/
svn -q add --force <slug>-svn/trunk
svn status <slug>-svn
svn commit <slug>-svn -m "Release <version>"
svn copy https://plugins.svn.wordpress.org/<approved-slug>/trunk \
  https://plugins.svn.wordpress.org/<approved-slug>/tags/<version> \
  -m "Tag <version>"
```

Before committing, check that the plugin-header version, `readme.txt` stable
tag, generated archive, and SVN tag are exactly the same version. Upload only
approved directory icons, banners, and screenshots separately to SVN's
`/assets/` directory; never add them to the plugin release ZIP unless they are
actually runtime assets.

Every SVN code or readme commit can regenerate the public ZIP, so only commit
reviewed release-ready material. Keep the public support and security response
arrangements active once a plugin is listed.

## Authoritative references

- [WordPress.org Plugin Directory overview](https://developer.wordpress.org/plugins/wordpress-org/)
- [Planning, submitting, and maintaining plugins](https://developer.wordpress.org/plugins/wordpress-org/planning-submitting-and-maintaining-plugins/)
- [Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [Plugin Developer FAQ](https://developer.wordpress.org/plugins/wordpress-org/plugin-developer-faq/)
- [Using Subversion](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)
