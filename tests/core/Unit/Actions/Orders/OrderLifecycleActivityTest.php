<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Core\Contracts\Actions\Orders\CancelsOrder;
use Lunar\Core\Contracts\Actions\Orders\ClosesOrder;
use Lunar\Core\Contracts\Actions\Orders\ReopensOrder;
use Lunar\Core\Models\Currency;
use Lunar\Core\Models\Language;
use Lunar\Core\Models\Location;
use Lunar\Core\Models\Order;
use Lunar\Tests\Core\Stubs\User;
use Lunar\Tests\Core\TestCase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Support\CauserResolver;

uses(TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    activity()->enableLogging();
    Language::factory()->create(['default' => true, 'code' => 'en']);
    Currency::factory()->create(['default' => true]);
    Location::factory()->default()->create();

    $this->order = Order::factory()->create();
});

function latestOrderActivity(Order $order): ?Activity
{
    return Activity::query()->where('subject_id', $order->id)->latest('id')->first();
}

it('logs a dedicated event when an order is closed and reopened', function () {
    app(ClosesOrder::class)->execute($this->order);
    expect(latestOrderActivity($this->order)->event)->toBe('order-closed');

    app(ReopensOrder::class)->execute($this->order->refresh());
    expect(latestOrderActivity($this->order)->event)->toBe('order-reopened');
});

it('logs a dedicated event with the reason when an order is cancelled', function () {
    app(CancelsOrder::class)->execute($this->order, 'items-unavailable', 'No stock');

    $log = Activity::query()->where('event', 'order-cancelled')->latest('id')->first();

    expect($log)->not->toBeNull()
        ->and($log->subject_id)->toBe($this->order->id)
        ->and($log->getProperty('reason'))->toBe('items-unavailable');
});

it('records the signed-in user as the causer by default', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    app(CancelsOrder::class)->execute($this->order, 'items-unavailable', 'No stock');

    expect(latestOrderActivity($this->order)->causer_id)->toBe($user->id);
});

it('resolves the causer through the activitylog causer resolver', function () {
    $signedIn = User::factory()->create();
    $actingFor = User::factory()->create();
    $this->actingAs($signedIn);

    app(CauserResolver::class)->resolveUsing(
        fn (mixed $subject) => $subject instanceof Model ? $subject : $actingFor
    );

    app(CancelsOrder::class)->execute($this->order, 'items-unavailable', 'No stock');

    expect(latestOrderActivity($this->order)->causer_id)->toBe($actingFor->id);
});

it('does not log a generic "updated" entry when closing', function () {
    app(ClosesOrder::class)->execute($this->order);

    expect(Activity::query()->where('subject_id', $this->order->id)->where('event', 'updated')->exists())
        ->toBeFalse();
});
