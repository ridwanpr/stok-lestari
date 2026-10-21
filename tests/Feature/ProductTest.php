<?php

use App\Models\Batch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guests are redirected to login from all product routes', function () {
    $product = Product::factory()->create();

    $this->get(route('products.index'))->assertRedirect(route('login'));
    $this->get(route('products.create'))->assertRedirect(route('login'));
    $this->post(route('products.store'), ['name' => 'Test', 'normal_price' => 1000])->assertRedirect(route('login'));
    $this->get(route('products.edit', $product))->assertRedirect(route('login'));
    $this->put(route('products.update', $product), ['name' => 'Test', 'normal_price' => 1000])->assertRedirect(route('login'));
    $this->delete(route('products.destroy', $product))->assertRedirect(route('login'));
});

test('index renders empty state when no products exist', function () {
    $seller = User::factory()->create();

    $response = $this->actingAs($seller)->get(route('products.index'));

    $response->assertOk();
    $response->assertSee('Belum ada produk terdaftar.');
    $response->assertSee(route('products.create'));
});

test('index displays products with formatted price and active batch stock sum', function () {
    $seller = User::factory()->create();

    $product = Product::factory()->create([
        'name' => 'Kopi Robusta 250g',
        'normal_price' => 45000,
    ]);

    // Active batch with 20 units
    Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 20,
        'status' => 'active',
    ]);

    // Another active batch with 15 units
    Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 15,
        'status' => 'active',
    ]);

    // Inactive batch (expired) with 10 units - should NOT count toward active stock
    Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 10,
        'status' => 'expired',
    ]);

    $response = $this->actingAs($seller)->get(route('products.index'));

    $response->assertOk();
    $response->assertSee('Kopi Robusta 250g');
    $response->assertSee('Rp 45.000');
    $response->assertSee('35 unit');
});

test('index search filters products by name', function () {
    $seller = User::factory()->create();

    Product::factory()->create(['name' => 'Minyak Goreng Kelapa']);
    Product::factory()->create(['name' => 'Gula Pasir Kristal']);

    $response = $this->actingAs($seller)->get(route('products.index', ['q' => 'Minyak']));

    $response->assertOk();
    $response->assertSee('Minyak Goreng Kelapa');
    $response->assertDontSee('Gula Pasir Kristal');
});

test('index search displays empty state when no matching products', function () {
    $seller = User::factory()->create();

    Product::factory()->create(['name' => 'Beras Pandan Wangi']);

    $response = $this->actingAs($seller)->get(route('products.index', ['q' => 'Cokelat']));

    $response->assertOk();
    $response->assertSee('Tidak ada produk yang cocok dengan');
    $response->assertSee('Cokelat');
    $response->assertDontSee('Beras Pandan Wangi');
});

test('create page renders for authenticated seller', function () {
    $seller = User::factory()->create();

    $response = $this->actingAs($seller)->get(route('products.create'));

    $response->assertOk();
    $response->assertSee('Tambah Produk Baru');
    $response->assertSee('Nama Produk');
    $response->assertSee('Harga Normal');
});

test('store validates required fields and rules', function () {
    $seller = User::factory()->create();
    Storage::fake('public');

    $response = $this->actingAs($seller)
        ->from(route('products.create'))
        ->post(route('products.store'), [
            'name' => '',
            'normal_price' => -500,
            'image' => UploadedFile::fake()->create('document.pdf', 500, 'application/pdf'),
        ]);

    $response->assertRedirect(route('products.create'));
    $response->assertSessionHasErrors(['name', 'normal_price', 'image']);
});

test('store validates maximum image file size', function () {
    $seller = User::factory()->create();
    Storage::fake('public');

    // 3 MB file exceeds 2 MB (2048 KB) limit
    $oversizedFile = UploadedFile::fake()->image('large.jpg')->size(3072);

    $response = $this->actingAs($seller)
        ->from(route('products.create'))
        ->post(route('products.store'), [
            'name' => 'Keripik Tempe',
            'normal_price' => 12000,
            'image' => $oversizedFile,
        ]);

    $response->assertRedirect(route('products.create'));
    $response->assertSessionHasErrors('image');
});

test('store successfully persists product without image and redirects', function () {
    $seller = User::factory()->create();

    $response = $this->actingAs($seller)->post(route('products.store'), [
        'name' => 'Susu Kedelai Alami',
        'normal_price' => 15000,
    ]);

    $response->assertRedirect(route('products.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('products', [
        'name' => 'Susu Kedelai Alami',
        'normal_price' => 15000,
        'image' => null,
    ]);
});

test('store successfully persists product with uploaded image', function () {
    $seller = User::factory()->create();
    Storage::fake('public');

    $image = UploadedFile::fake()->image('teh-melati.jpg', 400, 400);

    $response = $this->actingAs($seller)->post(route('products.store'), [
        'name' => 'Teh Melati Organik',
        'normal_price' => 18000,
        'image' => $image,
    ]);

    $response->assertRedirect(route('products.index'));

    $product = Product::where('name', 'Teh Melati Organik')->firstOrFail();
    expect($product->image)->not->toBeNull();
    Storage::disk('public')->assertExists($product->image);
});

test('edit page renders for authenticated seller', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Madu Hutan 500ml', 'normal_price' => 85000]);

    $response = $this->actingAs($seller)->get(route('products.edit', $product));

    $response->assertOk();
    $response->assertSee('Edit Produk');
    $response->assertSee('Madu Hutan 500ml');
    $response->assertSee('85000');
    $response->assertSee('Hapus Produk');
});

test('update validates required fields', function () {
    $seller = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($seller)
        ->from(route('products.edit', $product))
        ->put(route('products.update', $product), [
            'name' => '',
            'normal_price' => '',
        ]);

    $response->assertRedirect(route('products.edit', $product));
    $response->assertSessionHasErrors(['name', 'normal_price']);
});

test('update modifies product data without changing image when no new image is provided', function () {
    $seller = User::factory()->create();
    Storage::fake('public');

    $file = UploadedFile::fake()->image('initial.jpg');
    $path = $file->store('products', 'public');

    $product = Product::factory()->create([
        'name' => 'Nama Lama',
        'normal_price' => 20000,
        'image' => $path,
    ]);

    $response = $this->actingAs($seller)->put(route('products.update', $product), [
        'name' => 'Nama Baru',
        'normal_price' => 25000,
    ]);

    $response->assertRedirect(route('products.index'));
    $response->assertSessionHas('success');

    $product->refresh();
    expect($product->name)->toBe('Nama Baru')
        ->and($product->normal_price)->toBe(25000)
        ->and($product->image)->toBe($path);

    Storage::disk('public')->assertExists($path);
});

test('update replaces image, deletes old image file, and updates path', function () {
    $seller = User::factory()->create();
    Storage::fake('public');

    $oldFile = UploadedFile::fake()->image('old-pic.jpg');
    $oldPath = $oldFile->store('products', 'public');

    $product = Product::factory()->create([
        'name' => 'Kecap Manis',
        'normal_price' => 14000,
        'image' => $oldPath,
    ]);

    Storage::disk('public')->assertExists($oldPath);

    $newFile = UploadedFile::fake()->image('new-pic.jpg');

    $response = $this->actingAs($seller)->put(route('products.update', $product), [
        'name' => 'Kecap Manis Refill',
        'normal_price' => 16000,
        'image' => $newFile,
    ]);

    $response->assertRedirect(route('products.index'));

    $product->refresh();
    expect($product->image)->not->toBe($oldPath);

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($product->image);
});

test('destroy deletes product and associated image file', function () {
    $seller = User::factory()->create();
    Storage::fake('public');

    $file = UploadedFile::fake()->image('snack.jpg');
    $path = $file->store('products', 'public');

    $product = Product::factory()->create([
        'name' => 'Keripik Singkong',
        'normal_price' => 10000,
        'image' => $path,
    ]);

    Storage::disk('public')->assertExists($path);

    $response = $this->actingAs($seller)->delete(route('products.destroy', $product));

    $response->assertRedirect(route('products.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    Storage::disk('public')->assertMissing($path);
});

test('destroy cascades and deletes associated batches', function () {
    $seller = User::factory()->create();

    $product = Product::factory()->create();

    $batch1 = Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 10,
        'status' => 'active',
    ]);

    $batch2 = Batch::factory()->create([
        'product_id' => $product->id,
        'stock' => 20,
        'status' => 'expired',
    ]);

    $response = $this->actingAs($seller)->delete(route('products.destroy', $product));

    $response->assertRedirect(route('products.index'));

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    $this->assertDatabaseMissing('batches', ['id' => $batch1->id]);
    $this->assertDatabaseMissing('batches', ['id' => $batch2->id]);
});
