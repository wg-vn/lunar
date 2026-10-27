<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Core\Models\Currency;
use Lunar\Core\Models\Language;
use Lunar\Core\Models\Location;
use Lunar\Core\Models\Order;
use Lunar\Core\Models\OrderLine;
use Lunar\Tests\Core\TestCase;
use Spatie\Activitylog\Models\Activity;

uses(TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    activity()->enableLogging();
    Language::factory()->create(['default' => true, 'code' => 'en']);
    Currency::factory()->create(['default' => true]);
    Location::factory()->default()->create();

    $this->order = Order::factory()->create();
    $this->line = OrderLine::factory()->create([
        'order_id' => $this->order->id,
        'type' => 'physical',
        'quantity' => 2,
    ]);
});

function latestFulfilmentActivity(): ?Activity
{
    return Activity::query()->where('event', 'fulfilment-update')->latest('id')->first();
}

it('logs a fulfilment state change against the order', function () {
    $fulfilment = $this->order->createFulfilment([$this->line->id => 2]);
    $fulfilment->ship();

    $log = latestFulfilmentActivity();

    expect($log)->not->toBeNull()
        ->and($log->subject_id)->toBe($this->order->id)
        ->and($log->getProperty('type'))->toBe('state')
        ->and($log->getProperty('from'))->toBe('pending')
        ->and($log->getProperty('to'))->toBe('shipped')
        ->and($log->getProperty('fulfilment_id'))->toBe($fulfilment->id);
});

it('logs a hold with its reason and a release', function () {
    $fulfilment = $this->order->createFulfilment([$this->line->id => 2]);

    $fulfilment->hold('out-of-stock');
    expect(latestFulfilmentActivity()->getProperty('type'))->toBe('held')
        ->and(latestFulfilmentActivity()->getProperty('reason'))->toBe('out-of-stock');

    $fulfilment->refresh()->release();
    expect(latestFulfilmentActivity()->getProperty('type'))->toBe('released');
});
