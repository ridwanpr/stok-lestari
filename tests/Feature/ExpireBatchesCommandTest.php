<?php

use App\Models\Batch;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('batches:expire updates past-due active batches and deactivates related flash sales', function () {
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

    $todayBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => '2026-10-27',
        'stock' => 10,
    ]);

    $this->artisan('batches:expire')
        ->expectsOutputToContain('Berhasil memperbarui 1 batch kedaluwarsa dan menonaktifkan 1 flash sale.')
        ->assertSuccessful();

    expect($expiredBatch->fresh()->status)->toBe('expired');
    expect($activeFlashSale->fresh()->is_active)->toBeFalse();

    expect($futureBatch->fresh()->status)->toBe('active');
    expect($futureFlashSale->fresh()->is_active)->toBeTrue();

    expect($todayBatch->fresh()->status)->toBe('active');

    Carbon::setTestNow();
});

test('batches:expire outputs notice when no expired active batches exist', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $this->artisan('batches:expire')
        ->expectsOutput('Tidak ada batch kedaluwarsa yang perlu diproses.')
        ->assertSuccessful();

    Carbon::setTestNow();
});

test('batch active scope excludes past-due batches even if status is active', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $product = Product::factory()->create();

    $expiredActiveBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => '2026-10-26',
        'stock' => 5,
    ]);

    $validBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => '2026-10-27',
        'stock' => 10,
    ]);

    $activeBatches = Batch::query()->active()->pluck('id');

    expect($activeBatches)->toContain($validBatch->id);
    expect($activeBatches)->not->toContain($expiredActiveBatch->id);

    Carbon::setTestNow();
});

test('dashboard ignores expired batches for total stock and active flash sales count', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $user = User::factory()->create();
    $product = Product::factory()->create();

    $expiredBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => '2026-10-26',
        'stock' => 100,
    ]);

    FlashSale::factory()->create([
        'batch_id' => $expiredBatch->id,
        'is_active' => true,
    ]);

    $validBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => '2026-10-28',
        'stock' => 25,
    ]);

    FlashSale::factory()->create([
        'batch_id' => $validBatch->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewHas('totalStock', 25);
    $response->assertViewHas('activeFlashSalesCount', 1);

    Carbon::setTestNow();
});
