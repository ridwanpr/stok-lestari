<?php

use App\Models\Batch;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('guests are redirected to login from protected seller routes', function (string $routeName) {
    $response = $this->get(route($routeName));

    $response->assertRedirect(route('login'));
})->with([
    'dashboard',
    'products.index',
    'flash-sales.index',
]);

test('authenticated sellers can view protected routes', function (string $routeName, string $expectedContent) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route($routeName));

    $response->assertOk();
    $response->assertSee($expectedContent);
})->with([
    ['dashboard', 'Ringkasan Inventaris'],
    ['products.index', 'Produk'],
    ['flash-sales.index', 'Flash Sale'],
]);

test('dashboard shows real computed counts and expiring soon batches', function () {
    Carbon::setTestNow('2026-10-21 10:00:00');

    $seller = User::factory()->create();

    $productA = Product::factory()->create(['name' => 'Biskuit Gandum']);
    $productB = Product::factory()->create(['name' => 'Susu UHT Segar']);

    // Product A batches
    $expiringBatch1 = Batch::factory()->create([
        'product_id' => $productA->id,
        'stock' => 15,
        'expiry_date' => '2026-10-24',
        'status' => 'active',
    ]);

    Batch::factory()->create([
        'product_id' => $productA->id,
        'stock' => 25,
        'expiry_date' => '2026-11-20',
        'status' => 'active',
    ]);

    Batch::factory()->create([
        'product_id' => $productA->id,
        'stock' => 5,
        'expiry_date' => '2026-10-18',
        'status' => 'expired',
    ]);

    // Product B batches
    $expiringBatch2 = Batch::factory()->create([
        'product_id' => $productB->id,
        'stock' => 10,
        'expiry_date' => '2026-10-27',
        'status' => 'active',
    ]);

    Batch::factory()->create([
        'product_id' => $productB->id,
        'stock' => 30,
        'expiry_date' => '2026-10-23',
        'status' => 'sold_out',
    ]);

    // Flash sales: 1 active, 1 inactive
    FlashSale::factory()->create([
        'batch_id' => $expiringBatch1->id,
        'is_active' => true,
    ]);

    FlashSale::factory()->create([
        'batch_id' => $expiringBatch2->id,
        'is_active' => false,
    ]);

    $response = $this->actingAs($seller)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewHas('totalProducts', 2);
    $response->assertViewHas('totalStock', 50); // 15 + 25 + 10 active units
    $response->assertViewHas('expiringBatchesCount', 2);
    $response->assertViewHas('activeFlashSalesCount', 1);

    $response->assertSee('Biskuit Gandum');
    $response->assertSee('Susu UHT Segar');
    $response->assertSee('3 hari lagi');
    $response->assertSee('6 hari lagi');

    Carbon::setTestNow();
});

test('dashboard renders clean empty state when no batches are expiring soon', function () {
    Carbon::setTestNow('2026-10-21 10:00:00');

    $seller = User::factory()->create();

    $product = Product::factory()->create();
    Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 20,
        'expiry_date' => '2026-11-30',
        'status' => 'active',
    ]);

    $response = $this->actingAs($seller)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewHas('expiringBatchesCount', 0);
    $response->assertSee('Aman, tidak ada batch kedaluwarsa segera.');

    Carbon::setTestNow();
});
