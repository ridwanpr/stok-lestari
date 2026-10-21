<?php

use App\Models\Batch;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('guests are redirected to login from all batch routes', function () {
    $product = Product::factory()->create();
    $batch = Batch::factory()->create(['product_id' => $product->id]);

    $this->post(route('products.batches.store', $product), [
        'stock' => 10,
        'expiry_date' => now()->addDays(10)->toDateString(),
        'status' => 'active',
    ])->assertRedirect(route('login'));

    $this->get(route('batches.edit', $batch))->assertRedirect(route('login'));

    $this->put(route('batches.update', $batch), [
        'stock' => 15,
        'expiry_date' => now()->addDays(10)->toDateString(),
        'status' => 'active',
    ])->assertRedirect(route('login'));

    $this->delete(route('batches.destroy', $batch))->assertRedirect(route('login'));
});

test('store batch validates required fields and types', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($seller)
        ->from(route('products.edit', $product))
        ->post(route('products.batches.store', $product), [
            'stock' => 'bukan-angka',
            'expiry_date' => 'bukan-tanggal',
            'status' => 'invalid_status',
        ]);

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHasErrors(['stock', 'expiry_date', 'status']);
});

test('store batch rejects negative stock', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($seller)
        ->from(route('products.edit', $product))
        ->post(route('products.batches.store', $product), [
            'stock' => -5,
            'expiry_date' => now()->addDays(5)->toDateString(),
            'status' => 'active',
        ]);

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHasErrors('stock');
});

test('store batch rejects expiry date before today', function () {
    Carbon::setTestNow('2026-10-21 10:00:00');

    $seller = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($seller)
        ->from(route('products.edit', $product))
        ->post(route('products.batches.store', $product), [
            'stock' => 10,
            'expiry_date' => '2026-10-20',
            'status' => 'active',
        ]);

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHasErrors('expiry_date');

    Carbon::setTestNow();
});

test('store batch successfully persists batch and redirects back to product edit', function () {
    Carbon::setTestNow('2026-10-21 10:00:00');

    $seller = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($seller)->post(route('products.batches.store', $product), [
        'stock' => 25,
        'expiry_date' => '2026-11-15',
        'status' => 'active',
    ]);

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHas('success', 'Batch berhasil ditambahkan.');

    $this->assertDatabaseHas('batches', [
        'product_id' => $product->id,
        'stock' => 25,
        'status' => 'active',
    ]);

    $batch = Batch::where('product_id', $product->id)->firstOrFail();
    expect($batch->expiry_date->format('Y-m-d'))->toBe('2026-11-15');

    Carbon::setTestNow();
});

test('store batch preserves manual status without auto flipping', function () {
    Carbon::setTestNow('2026-10-21 10:00:00');

    $seller = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($seller)->post(route('products.batches.store', $product), [
        'stock' => 0,
        'expiry_date' => '2026-11-01',
        'status' => 'sold_out',
    ]);

    $response->assertRedirect(route('products.edit', $product));

    $this->assertDatabaseHas('batches', [
        'product_id' => $product->id,
        'stock' => 0,
        'status' => 'sold_out',
    ]);

    Carbon::setTestNow();
});

test('edit batch page renders for authenticated seller', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Kecap Manis Cap Bango']);
    $batch = Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 40,
        'expiry_date' => '2026-12-01',
        'status' => 'active',
    ]);

    $response = $this->actingAs($seller)->get(route('batches.edit', $batch));

    $response->assertOk();
    $response->assertSee('Edit Batch #'.$batch->id);
    $response->assertSee('Kecap Manis Cap Bango');
    $response->assertSee('40');
    $response->assertSee('2026-12-01');
});

test('update batch validates required fields', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();
    $batch = Batch::factory()->create(['product_id' => $product->id]);

    $response = $this->actingAs($seller)
        ->from(route('batches.edit', $batch))
        ->put(route('batches.update', $batch), [
            'stock' => -1,
            'expiry_date' => 'invalid-date',
            'status' => 'unknown',
        ]);

    $response->assertRedirect(route('batches.edit', $batch));
    $response->assertSessionHasErrors(['stock', 'expiry_date', 'status']);
});

test('update batch allows past expiry date to correct old inventory', function () {
    Carbon::setTestNow('2026-10-21 10:00:00');

    $seller = User::factory()->create();
    $product = Product::factory()->create();
    $batch = Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 10,
        'expiry_date' => '2026-11-01',
        'status' => 'active',
    ]);

    $response = $this->actingAs($seller)->put(route('batches.update', $batch), [
        'stock' => 8,
        'expiry_date' => '2026-10-15',
        'status' => 'expired',
    ]);

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHas('success', 'Batch berhasil diperbarui.');

    $batch->refresh();
    expect($batch->stock)->toBe(8)
        ->and($batch->expiry_date->format('Y-m-d'))->toBe('2026-10-15')
        ->and($batch->status)->toBe('expired');

    Carbon::setTestNow();
});

test('destroy batch removes batch from database and redirects to product edit', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();
    $batch = Batch::factory()->create(['product_id' => $product->id]);

    $response = $this->actingAs($seller)->delete(route('batches.destroy', $batch));

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHas('success', 'Batch berhasil dihapus.');

    $this->assertDatabaseMissing('batches', ['id' => $batch->id]);
});

test('destroy batch is blocked with an error if batch has an active flash sale', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();
    $batch = Batch::factory()->create(['product_id' => $product->id]);

    FlashSale::factory()->create([
        'batch_id' => $batch->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($seller)->delete(route('batches.destroy', $batch));

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHas('error', 'Nonaktifkan flash sale terlebih dahulu sebelum menghapus batch ini.');

    $this->assertDatabaseHas('batches', ['id' => $batch->id]);
});

test('destroy batch succeeds when batch only has inactive flash sales', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();
    $batch = Batch::factory()->create(['product_id' => $product->id]);

    $sale = FlashSale::factory()->create([
        'batch_id' => $batch->id,
        'is_active' => false,
    ]);

    $response = $this->actingAs($seller)->delete(route('batches.destroy', $batch));

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHas('success', 'Batch berhasil dihapus.');

    $this->assertDatabaseMissing('batches', ['id' => $batch->id]);
    $this->assertDatabaseMissing('flash_sales', ['id' => $sale->id]);
});

test('product edit view renders empty state when product has no batches', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($seller)->get(route('products.edit', $product));

    $response->assertOk();
    $response->assertSee('Batch &amp; Stok', false);
    $response->assertSee('Belum ada batch untuk produk ini.');
    $response->assertSee('Tambah Batch');
});

test('product edit view renders batches ordered by expiry date with flash sale pill and formatted dates', function () {
    Carbon::setTestNow('2026-10-21 10:00:00');

    $seller = User::factory()->create();
    $product = Product::factory()->create();

    $batchLater = Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 50,
        'expiry_date' => '2026-12-10',
        'status' => 'active',
    ]);

    $batchSoon = Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 12,
        'expiry_date' => '2026-10-25',
        'status' => 'active',
    ]);

    FlashSale::factory()->create([
        'batch_id' => $batchSoon->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($seller)->get(route('products.edit', $product));

    $response->assertOk();
    $response->assertSee('12 unit');
    $response->assertSee('50 unit');
    $response->assertSee('25/10/2026');
    $response->assertSee('10/12/2026');
    $response->assertSee('Flash Sale');

    // Ordered ascending: 25/10/2026 appears before 10/12/2026 in the HTML
    $content = $response->getContent();
    $posSoon = strpos($content, '25/10/2026');
    $posLater = strpos($content, '10/12/2026');
    expect($posSoon)->toBeLessThan($posLater);

    Carbon::setTestNow();
});
