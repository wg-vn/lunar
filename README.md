# wg-vn/lunar-core

Lunar's e-commerce core for Laravel, forked from the `2.x` line of
[lunarphp/lunar](https://github.com/lunarphp/lunar) to run on the current majors of the Spatie and
Scout packages a host app is likely to use already. Not affiliated with LunarPHP Ltd.

This repository is a copy of the upstream monorepo. Only `packages/core` is published, as
`wg-vn/lunar-core`, and it replaces `lunarphp/core`, so Lunar add-ons that require `lunarphp/core`
install on top of it. The PHP namespace stays `Lunar\Core\`. For everything Lunar itself does, see the
[Lunar documentation](https://docs.lunarphp.com/).

## How it differs from upstream

| | Upstream `lunarphp/core` 2.x | `wg-vn/lunar-core` |
| --- | --- | --- |
| `spatie/laravel-activitylog` | ^4.10 | ^5.1 |
| `laravel/scout` | ^10.13 | ^11.7 |
| `spatie/laravel-permission` | ^6.12 | ^8.3 |
| `activity_log` and permission-table migrations | shipped in core | not shipped; the host app owns these tables |
| Staff roles and permissions seeded after `migrate` | always | behind `lunar.staff.seed_roles_and_permissions` (default `true`) |
| Causer of core's own activity entries | `auth()->user()`, set explicitly | activitylog's `CauserResolver` (the signed-in user unless the app overrides it) |
| `media` table migration | always runs | behind `lunar.database.create_media_table` (default `true`) |

Core's own migrations for the `activity_log` and permission tables skipped tables that already
existed on the way up, but dropped them on the way down. Rolling back Lunar's migrations in an app
that owns those tables deleted its audit log and every role and permission.

## Installation

```bash
composer require wg-vn/lunar-core:^2.0@alpha
```

The host app must create the `activity_log` table and the permission tables from
`spatie/laravel-activitylog` 5 and `spatie/laravel-permission` 8 (publish their migrations).

An app that runs no Lunar staff panel and keeps its own roles in the permission tables should
publish Lunar's config and set:

```php
// config/lunar/staff.php
'register_guard' => false,
'seed_roles_and_permissions' => false,
```

An app that creates spatie/laravel-medialibrary's `media` table in its own migration should set:

```php
// config/lunar/database.php
'create_media_table' => false,
```

## Keeping up with upstream

```bash
git fetch upstream 2.x            # upstream = https://github.com/lunarphp/lunar.git, fetched with --no-tags
git merge upstream/2.x
```

Conflicts land in the files this fork owns: `composer.json`, `.gitattributes`, `.github/workflows`,
`README.md`, `CHANGELOG.md`, `phpstan.neon.dist`, plus any core file changed above. Keep the fork's
side of each. A new upstream dependency in `packages/core/composer.json` goes into the root
`composer.json` too, since the root file is the one Packagist publishes.

Then run the checks CI runs:

```bash
vendor/bin/pest --testsuite core --parallel
vendor/bin/phpstan analyse --no-progress
vendor/bin/pint --test packages/core tests/core
```

## Releasing

Versions follow upstream's with one more segment: the first release built on upstream
`2.0.0-alpha.6` is `v2.0.0-alpha.6.1`, the next one `v2.0.0-alpha.6.2`, and the first after merging
upstream `2.0.0-alpha.7` is `v2.0.0-alpha.7.1`.

1. Add a `## [x.y.z...]` section to `CHANGELOG.md`.
2. Tag and push that tag only: `git tag v2.0.0-alpha.6.1 && git push origin v2.0.0-alpha.6.1`.
   Never `git push --tags`: the local clone may hold upstream's tags.
3. The `Publish to Packagist` workflow asks Packagist to re-read the repository and creates a GitHub
   Release from the changelog section.

The workflow updates an existing Packagist package. The first time, submit
`https://github.com/wg-vn/lunar` once at <https://packagist.org/packages/submit>.

## License

MIT. Lunar is copyright LunarPHP Ltd; see [LICENSE.md](LICENSE.md).
