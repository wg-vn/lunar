<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Lunar\Tests\Core\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\artisan;

uses(TestCase::class);

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::query()->delete();
    Permission::query()->delete();
});

test('migrating seeds the base staff roles and permissions', function () {
    artisan('migrate');

    expect(Role::where('guard_name', 'staff')->pluck('name')->all())->toEqualCanonicalizing(['admin', 'staff'])
        ->and(Permission::where('guard_name', 'staff')->count())->toBeGreaterThan(0);
});

test('migrating seeds no staff roles or permissions when seeding is switched off', function () {
    Config::set('lunar.staff.seed_roles_and_permissions', false);

    artisan('migrate');

    expect(Role::count())->toBe(0)
        ->and(Permission::count())->toBe(0);
});
