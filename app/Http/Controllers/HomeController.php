<?php

namespace App\Http\Controllers;

use App\Models\FlashSale;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Handle display of public cuci gudang showcase.
     */
    public function __invoke(): View
    {
        $flashSales = FlashSale::query()
            ->active()
            ->whereHas('batch', function ($query) {
                $query->active()
                    ->where('stock', '>', 0)
                    ->whereDate('expiry_date', '>=', now()->toDateString());
            })
            ->with(['batch.product'])
            ->latest('id')
            ->get();

        return view('home', [
            'flashSales' => $flashSales,
        ]);
    }
}
