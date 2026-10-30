<?php

use App\Models\Batch;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('cron.token', 'test-secret-cron-token');
});

test('returns 403 when cron token is not configured on the server', function () {
    Config::set('cron.token', null);

    $response = $this->getJson(route('cron.expire-batches', ['token' => 'some-token']));

    $response->assertStatus(403)
        ->assertJson([
            'message' => 'Cron webhook is disabled or token is not configured.',
        ]);
});

test('returns 401 when no token is provided', function () {
    $response = $this->getJson(route('cron.expire-batches'));

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthorized.',
        ]);
});

test('returns 401 when an invalid token is provided', function () {
    $response = $this->getJson(route('cron.expire-batches', ['token' => 'invalid-token']));

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthorized.',
        ]);
});

test('authenticates successfully via query parameter for GET and POST', function () {
    $responseGet = $this->getJson(route('cron.expire-batches', ['token' => 'test-secret-cron-token']));
    $responseGet->assertOk()
        ->assertJsonPath('message', 'Batches expiry check completed successfully.');

    $responsePost = $this->postJson(route('cron.expire-batches', ['token' => 'test-secret-cron-token']));
    $responsePost->assertOk()
        ->assertJsonPath('message', 'Batches expiry check completed successfully.');
});

test('authenticates successfully via X-Cron-Token header', function () {
    $response = $this->withHeaders([
        'X-Cron-Token' => 'test-secret-cron-token',
    ])->getJson(route('cron.expire-batches'));

    $response->assertOk()
        ->assertJsonPath('message', 'Batches expiry check completed successfully.');
});

test('authenticates successfully via Bearer token', function () {
    $response = $this->withToken('test-secret-cron-token')
        ->getJson(route('cron.expire-batches'));

    $response->assertOk()
        ->assertJsonPath('message', 'Batches expiry check completed successfully.');
});

test('webhook updates past-due active batches and deactivates related flash sales', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $product = Product::factory()->create();

    $expiredBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => '2026-10-26',
        'stock' => 15,
    ]);

    $activeFlashSale = FlashSale::factory()->create([
        'batch_id' => $expiredBatch->id,
        'is_active' => true,
    ]);

    $futureBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => '2026-10-28',
        'stock' => 20,
    ]);

    $futureFlashSale = FlashSale::factory()->create([
        'batch_id' => $futureBatch->id,
        'is_active' => true,
    ]);

    $response = $this->withToken('test-secret-cron-token')
        ->postJson(route('cron.expire-batches'));

    $response->assertOk()
        ->assertJson([
            'message' => 'Batches expiry check completed successfully.',
            'data' => [
                'expired_batches' => 1,
                'deactivated_flash_sales' => 1,
            ],
        ]);

    expect($expiredBatch->fresh()->status)->toBe('expired');
    expect($activeFlashSale->fresh()->is_active)->toBeFalse();

    expect($futureBatch->fresh()->status)->toBe('active');
    expect($futureFlashSale->fresh()->is_active)->toBeTrue();

    Carbon::setTestNow();
});

test('webhook endpoint rate limits excessive requests', function () {
    for ($i = 0; $i < 10; $i++) {
        $this->getJson(route('cron.expire-batches', ['token' => 'test-secret-cron-token']))
            ->assertOk();
    }

    $response = $this->getJson(route('cron.expire-batches', ['token' => 'test-secret-cron-token']));
    $response->assertStatus(429);
});
