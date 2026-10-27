<?php

namespace Lunar\Tests\Core;

use Illuminate\Support\Facades\Config;
use Lunar\Core\Facades\Taxes;
use Lunar\Core\LunarServiceProvider;
use Lunar\Nestedset\NestedSetServiceProvider;
use Lunar\Tests\Core\Stubs\TestTaxDriver;
use Lunar\Tests\Core\Stubs\TestUrlGenerator;
use Lunar\Tests\Core\Stubs\User;
use Lunar\Tests\TestCase as BaseTestCase;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\LaravelBlink\BlinkServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

use function Orchestra\Testbench\after_resolving;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup
        Config::set('providers.users.model', User::class);
        Config::set('lunar.urls.generator', TestUrlGenerator::class);
        Config::set('lunar.taxes.driver', 'test');
        Config::set('lunar.media.collection', 'images');

        Taxes::extend('test', function ($app) {
            return $app->make(TestTaxDriver::class);
        });

        activity()->disableLogging();

        // Freeze time to avoid timestamp errors
        $this->freezeTime();
    }

    // The activity log and permission tables belong to the host app, so core's
    // suite creates them from its own copies of the spatie migration stubs.
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();

        after_resolving($this->app, 'migrator', static function ($migrator) {
            $migrator->path(__DIR__.'/database/migrations');
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            LunarServiceProvider::class,
            MediaLibraryServiceProvider::class,
            ActivitylogServiceProvider::class,
            NestedSetServiceProvider::class,
            BlinkServiceProvider::class,
            PermissionServiceProvider::class,
        ];
    }
}
