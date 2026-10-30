<?php

namespace App\Actions;

use App\Models\Batch;
use App\Models\FlashSale;

class ExpireBatchesAction
{
    /**
     * Execute batch expiration and deactivate associated flash sales.
     *
     * @return array{expired_batches: int, deactivated_flash_sales: int}
     */
    public function execute(): array
    {
        $today = now()->toDateString();

        $expiredBatchIds = Batch::query()
            ->where('status', 'active')
            ->whereDate('expiry_date', '<', $today)
            ->pluck('id');

        if ($expiredBatchIds->isEmpty()) {
            return [
                'expired_batches' => 0,
                'deactivated_flash_sales' => 0,
            ];
        }

        $batchesCount = Batch::query()
            ->whereIn('id', $expiredBatchIds)
            ->update(['status' => 'expired']);

        $flashSalesCount = FlashSale::query()
            ->whereIn('batch_id', $expiredBatchIds)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return [
            'expired_batches' => $batchesCount,
            'deactivated_flash_sales' => $flashSalesCount,
        ];
    }
}
