<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlashSaleRequest;
use App\Models\Batch;
use App\Models\FlashSale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    /**
     * Display seller flash sale management & creation form.
     */
    public function index(Request $request): View
    {
        $flashSales = FlashSale::query()
            ->with(['batch.product'])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $availableBatches = Batch::query()
            ->active()
            ->where('stock', '>', 0)
            ->whereDate('expiry_date', '>=', now()->toDateString())
            ->whereDoesntHave('flashSales', function ($query) {
                $query->active();
            })
            ->with('product')
            ->orderBy('expiry_date', 'asc')
            ->get();

        $selectedBatchId = $request->integer('batch_id');

        return view('flash-sales.index', [
            'flashSales' => $flashSales,
            'availableBatches' => $availableBatches,
            'selectedBatchId' => $selectedBatchId ?: null,
        ]);
    }

    /**
     * Store a new flash sale for a batch.
     */
    public function store(FlashSaleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = true;

        FlashSale::create($data);

        return redirect()
            ->route('flash-sales.index')
            ->with('success', 'Promo flash sale cuci gudang berhasil dibuat.');
    }

    /**
     * Toggle flash sale active status.
     */
    public function toggle(FlashSale $flashSale): RedirectResponse
    {
        $newStatus = ! $flashSale->is_active;

        if ($newStatus) {
            $batch = $flashSale->batch;

            if (! $batch || $batch->status !== 'active' || $batch->expiry_date->isPast() && ! $batch->expiry_date->isToday()) {
                return redirect()
                    ->route('flash-sales.index')
                    ->with('error', 'Flash sale tidak dapat diaktifkan karena batch sudah kedaluwarsa atau tidak aktif.');
            }

            if ($batch->stock <= 0) {
                return redirect()
                    ->route('flash-sales.index')
                    ->with('error', 'Flash sale tidak dapat diaktifkan karena stok batch sudah habis.');
            }

            $hasOtherActive = FlashSale::query()
                ->where('batch_id', $flashSale->batch_id)
                ->where('id', '!=', $flashSale->id)
                ->active()
                ->exists();

            if ($hasOtherActive) {
                return redirect()
                    ->route('flash-sales.index')
                    ->with('error', 'Batch ini sudah memiliki promo flash sale lain yang sedang aktif.');
            }
        }

        $flashSale->update(['is_active' => $newStatus]);

        $message = $newStatus
            ? 'Promo flash sale berhasil diaktifkan kembali.'
            : 'Promo flash sale berhasil dinonaktifkan.';

        return redirect()
            ->route('flash-sales.index')
            ->with('success', $message);
    }

    /**
     * Remove a flash sale.
     */
    public function destroy(FlashSale $flashSale): RedirectResponse
    {
        $flashSale->delete();

        return redirect()
            ->route('flash-sales.index')
            ->with('success', 'Promo flash sale berhasil dihapus.');
    }
}
