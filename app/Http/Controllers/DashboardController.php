<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalProducts = Product::query()->count();
        $totalStock = (int) Batch::query()->active()->sum('stock');
        $expiringBatchesCount = Batch::query()->expiringSoon(7)->count();
        $activeFlashSalesCount = FlashSale::query()->active()->count();

        $expiringBatches = Batch::query()
            ->with(['product', 'flashSales' => fn ($q) => $q->active()])
            ->expiringSoon(7)
            ->orderBy('expiry_date', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
            'expiringBatchesCount' => $expiringBatchesCount,
            'activeFlashSalesCount' => $activeFlashSalesCount,
            'expiringBatches' => $expiringBatches,
        ]);
    }
}
