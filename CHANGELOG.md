# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/). Versions follow
upstream Lunar's with one more segment; see the Releasing section of the README.

## [2.0.0-alpha.6.1] - 2026-10-26

Built from upstream `lunarphp/lunar` `2.x` at `7f464be7` (`2.0.0-alpha.6` plus 11 commits).

### Changed
- Published as `wg-vn/lunar-core`, replacing `lunarphp/core`.
- Requires `spatie/laravel-activitylog` ^5.1 (was ^4.10.1), `laravel/scout` ^11.7 (was ^10.13.1)
  and `spatie/laravel-permission` ^8.3 (was ^6.12).
- The `LogsActivity` model concern uses activitylog 5's namespaces and `dontLogEmptyChanges()`.

### Added
- `lunar.staff.seed_roles_and_permissions` config switch (default `true`). When `false`, migrating
  writes no staff roles or permissions into the permission tables.

### Removed
- Core's `create_activity_log_table` and `create_permission_tables` migrations. The host app creates
  those tables; the removed migrations dropped them on rollback.
