<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Lunar\Tests\Core\TestCase;

uses(TestCase::class);

uses(RefreshDatabase::class);

function coreMediaTableMigration(): object
{
    return require __DIR__.'/../../../../packages/core/database/migrations/2026_01_01_000011_create_media_table.php';
}

test('creates the media table when it is missing', function () {
    Schema::drop('media');

    coreMediaTableMigration()->up();

    expect(Schema::hasTable('media'))->toBeTrue();
});

test('leaves the media table to the host app when switched off', function () {
    Config::set('lunar.database.create_media_table', false);
    Schema::drop('media');

    coreMediaTableMigration()->up();

    expect(Schema::hasTable('media'))->toBeFalse();
});

test('keeps the host app media table on rollback when switched off', function () {
    Config::set('lunar.database.create_media_table', false);

    coreMediaTableMigration()->down();

    expect(Schema::hasTable('media'))->toBeTrue();
});
