<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchRequest;
use App\Models\Batch;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BatchController extends Controller
{
    public function store(BatchRequest $request, Product $product): RedirectResponse
    {
        $product->batches()->create($request->validated());

        return redirect()
            ->route('products.edit', $product)
            ->with('success', 'Batch berhasil ditambahkan.');
    }

    public function edit(Batch $batch): View
    {
        return view('batches.edit', [
            'batch' => $batch,
            'product' => $batch->product,
        ]);
    }

    public function update(BatchRequest $request, Batch $batch): RedirectResponse
    {
        $batch->update($request->validated());

        return redirect()
            ->route('products.edit', $batch->product_id)
            ->with('success', 'Batch berhasil diperbarui.');
    }

    public function destroy(Batch $batch): RedirectResponse
    {
        if ($batch->flashSales()->active()->exists()) {
            return redirect()
                ->route('products.edit', $batch->product_id)
                ->with('error', 'Nonaktifkan flash sale terlebih dahulu sebelum menghapus batch ini.');
        }

        $productId = $batch->product_id;
        $batch->delete();

        return redirect()
            ->route('products.edit', $productId)
            ->with('success', 'Batch berhasil dihapus.');
    }
}
