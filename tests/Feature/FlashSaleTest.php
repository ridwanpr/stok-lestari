<?php

use App\Models\Batch;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('guests are redirected to login from flash sale management routes', function () {
    $flashSale = FlashSale::factory()->create();

    $this->get(route('flash-sales.index'))->assertRedirect(route('login'));
    $this->post(route('flash-sales.store'), [])->assertRedirect(route('login'));
    $this->patch(route('flash-sales.toggle', $flashSale))->assertRedirect(route('login'));
    $this->delete(route('flash-sales.destroy', $flashSale))->assertRedirect(route('login'));
});

test('public guests can view home showcase with active cuci gudang products', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $productActive = Product::factory()->create([
        'name' => 'Kopi Robusta Lampung',
        'normal_price' => 30000,
    ]);

    $batchActive = Batch::factory()->create([
        'product_id' => $productActive->id,
        'stock' => 12,
        'expiry_date' => '2026-10-30',
        'status' => 'active',
    ]);

    FlashSale::factory()->create([
        'batch_id' => $batchActive->id,
        'discount_price' => 15000,
        'is_active' => true,
    ]);

    // Inactive flash sale
    $productInactive = Product::factory()->create(['name' => 'Teh Melati Wangi']);
    $batchInactive = Batch::factory()->create([
        'product_id' => $productInactive->id,
        'stock' => 5,
        'expiry_date' => '2026-10-31',
        'status' => 'active',
    ]);
    FlashSale::factory()->create([
        'batch_id' => $batchInactive->id,
        'discount_price' => 8000,
        'is_active' => false,
    ]);

    // Expired batch with active flash sale (should not show)
    $productExpired = Product::factory()->create(['name' => 'Roti Gandum']);
    $batchExpired = Batch::factory()->create([
        'product_id' => $productExpired->id,
        'stock' => 5,
        'expiry_date' => '2026-10-25',
        'status' => 'active',
    ]);
    FlashSale::factory()->create([
        'batch_id' => $batchExpired->id,
        'discount_price' => 5000,
        'is_active' => true,
    ]);

    // Zero stock batch with active flash sale (should not show)
    $productZeroStock = Product::factory()->create(['name' => 'Susu Almond']);
    $batchZeroStock = Batch::factory()->create([
        'product_id' => $productZeroStock->id,
        'stock' => 0,
        'expiry_date' => '2026-10-31',
        'status' => 'active',
    ]);
    FlashSale::factory()->create([
        'batch_id' => $batchZeroStock->id,
        'discount_price' => 12000,
        'is_active' => true,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Cuci Gudang');
    $response->assertSee('Kopi Robusta Lampung');
    $response->assertSee('15.000');
    $response->assertSee('30.000');
    $response->assertSee('-50%');
    $response->assertSee('12');
    $response->assertSee(route('login'));
    $response->assertDontSee('Teh Melati Wangi');
    $response->assertDontSee('Roti Gandum');
    $response->assertDontSee('Susu Almond');

    Carbon::setTestNow();
});

test('public home navbar shows dashboard link when authenticated', function () {
    $seller = User::factory()->create();

    $response = $this->actingAs($seller)->get(route('home'));

    $response->assertOk();
    $response->assertSee(route('dashboard'));
    $response->assertSee('Dashboard');
});

test('public home shows empty state when no active flash sales exist', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Belum Ada Promo Aktif');
});

test('seller can view flash sale index page with available batches and active list', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $seller = User::factory()->create();

    $product = Product::factory()->create(['name' => 'Snack Kentang']);
    $batch = Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 15,
        'expiry_date' => '2026-10-29',
        'status' => 'active',
    ]);

    $flashSale = FlashSale::factory()->create([
        'batch_id' => $batch->id,
        'discount_price' => 10000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($seller)->get(route('flash-sales.index'));

    $response->assertOk();
    $response->assertSee('Promo Flash Sale (Cuci Gudang)');
    $response->assertSee('Snack Kentang');
    $response->assertSee('10.000');
    $response->assertSee('Aktif');

    Carbon::setTestNow();
});

test('seller can prefill batch_id from query string in flash sale index', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();
    $batch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => now()->addDays(3),
    ]);

    $response = $this->actingAs($seller)->get(route('flash-sales.index', ['batch_id' => $batch->id]));

    $response->assertOk();
    $response->assertViewHas('selectedBatchId', $batch->id);
});

test('seller can create a new flash sale for a valid batch', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $seller = User::factory()->create();
    $product = Product::factory()->create(['normal_price' => 25000]);
    $batch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'stock' => 20,
        'expiry_date' => '2026-10-30',
    ]);

    $response = $this->actingAs($seller)->post(route('flash-sales.store'), [
        'batch_id' => $batch->id,
        'discount_price' => 12500,
    ]);

    $response->assertRedirect(route('flash-sales.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('flash_sales', [
        'batch_id' => $batch->id,
        'discount_price' => 12500,
        'is_active' => true,
    ]);

    Carbon::setTestNow();
});

test('validation rejects discount price greater than or equal to normal price', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create(['normal_price' => 20000]);
    $batch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => now()->addDays(5),
    ]);

    $response = $this->actingAs($seller)->post(route('flash-sales.store'), [
        'batch_id' => $batch->id,
        'discount_price' => 20000,
    ]);

    $response->assertSessionHasErrors('discount_price');
    $this->assertDatabaseEmpty('flash_sales');
});

test('validation rejects batch that already has an active flash sale', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create(['normal_price' => 30000]);
    $batch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => now()->addDays(5),
    ]);

    FlashSale::factory()->create([
        'batch_id' => $batch->id,
        'discount_price' => 15000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($seller)->post(route('flash-sales.store'), [
        'batch_id' => $batch->id,
        'discount_price' => 10000,
    ]);

    $response->assertSessionHasErrors('batch_id');
});

test('validation rejects batch that is expired or not active', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $seller = User::factory()->create();
    $product = Product::factory()->create(['normal_price' => 30000]);

    $expiredBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'expiry_date' => '2026-10-20',
    ]);

    $response = $this->actingAs($seller)->post(route('flash-sales.store'), [
        'batch_id' => $expiredBatch->id,
        'discount_price' => 15000,
    ]);

    $response->assertSessionHasErrors('batch_id');

    $inactiveBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'expired',
        'expiry_date' => '2026-10-30',
    ]);

    $response2 = $this->actingAs($seller)->post(route('flash-sales.store'), [
        'batch_id' => $inactiveBatch->id,
        'discount_price' => 15000,
    ]);

    $response2->assertSessionHasErrors('batch_id');

    Carbon::setTestNow();
});

test('validation rejects batch with zero stock', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create(['normal_price' => 30000]);
    $batch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'stock' => 0,
        'expiry_date' => now()->addDays(5),
    ]);

    $response = $this->actingAs($seller)->post(route('flash-sales.store'), [
        'batch_id' => $batch->id,
        'discount_price' => 15000,
    ]);

    $response->assertSessionHasErrors('batch_id');
});

test('toggle cannot reactivate flash sale if batch is expired, out of stock, or has duplicate active promo', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();

    // Expired batch
    $expiredBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'stock' => 10,
        'expiry_date' => now()->subDay(),
    ]);
    $flashSaleExpired = FlashSale::factory()->create([
        'batch_id' => $expiredBatch->id,
        'is_active' => false,
    ]);

    $this->actingAs($seller)->patch(route('flash-sales.toggle', $flashSaleExpired))
        ->assertRedirect(route('flash-sales.index'))
        ->assertSessionHas('error');
    expect($flashSaleExpired->fresh()->is_active)->toBeFalse();

    // Zero stock batch
    $zeroStockBatch = Batch::factory()->create([
        'product_id' => $product->id,
        'status' => 'active',
        'stock' => 0,
        'expiry_date' => now()->addDays(3),
    ]);
    $flashSaleZeroStock = FlashSale::factory()->create([
        'batch_id' => $zeroStockBatch->id,
        'is_active' => false,
    ]);

    $this->actingAs($seller)->patch(route('flash-sales.toggle', $flashSaleZeroStock))
        ->assertRedirect(route('flash-sales.index'))
        ->assertSessionHas('error');
    expect($flashSaleZeroStock->fresh()->is_active)->toBeFalse();
});

test('seller can toggle flash sale status', function () {
    $seller = User::factory()->create();
    $flashSale = FlashSale::factory()->create(['is_active' => true]);

    $response = $this->actingAs($seller)->patch(route('flash-sales.toggle', $flashSale));

    $response->assertRedirect(route('flash-sales.index'));
    expect($flashSale->fresh()->is_active)->toBeFalse();

    $this->actingAs($seller)->patch(route('flash-sales.toggle', $flashSale));
    expect($flashSale->fresh()->is_active)->toBeTrue();
});

test('seller can delete a flash sale', function () {
    $seller = User::factory()->create();
    $flashSale = FlashSale::factory()->create();

    $response = $this->actingAs($seller)->delete(route('flash-sales.destroy', $flashSale));

    $response->assertRedirect(route('flash-sales.index'));
    $this->assertDatabaseMissing('flash_sales', ['id' => $flashSale->id]);
});

test('dashboard shows shortcut button or active badge on expiring batches', function () {
    Carbon::setTestNow('2026-10-27 10:00:00');

    $seller = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Cokelat Bubuk']);

    $batchWithoutPromo = Batch::factory()->create([
        'product_id' => $product->id,
        'expiry_date' => '2026-10-29',
        'status' => 'active',
    ]);

    $batchWithPromo = Batch::factory()->create([
        'product_id' => $product->id,
        'expiry_date' => '2026-10-30',
        'status' => 'active',
    ]);

    FlashSale::factory()->create([
        'batch_id' => $batchWithPromo->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($seller)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee(route('flash-sales.index', ['batch_id' => $batchWithoutPromo->id]));
    $response->assertSee('+ Flash Sale');
    $response->assertSee('Flash Sale Aktif');

    Carbon::setTestNow();
});
