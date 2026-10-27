<?php

use Lunar\Core\Models\Staff;

return [
    /*
    |--------------------------------------------------------------------------
    | Staff auth guard
    |--------------------------------------------------------------------------
    |
    | The auth guard name used for staff accounts. Both Filament and the
    | future Inertia panel resolve permissions/policies against this guard.
    |
    */
    'guard' => 'staff',

    /*
    |--------------------------------------------------------------------------
    | Staff auth provider
    |--------------------------------------------------------------------------
    |
    | The eloquent provider name and model used to look up staff accounts.
    | Override the model when extending the Lunar Staff class downstream.
    |
    */
    'provider' => 'staff',

    'model' => Staff::class,

    /*
    |--------------------------------------------------------------------------
    | Register guard automatically
    |--------------------------------------------------------------------------
    |
    | When true Lunar will register the staff guard and provider on boot. Set
    | to false to manage guard/provider configuration manually in auth.php.
    |
    */
    'register_guard' => true,

    /*
    |--------------------------------------------------------------------------
    | Seed base roles and permissions
    |--------------------------------------------------------------------------
    |
    | When true Lunar writes its base staff roles and permissions into the
    | spatie/laravel-permission tables after every migrate. Set to false when
    | the app runs no Lunar staff panel and keeps its own roles in those tables.
    |
    */
    'seed_roles_and_permissions' => true,
];
