<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Models\FlashSale;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('batches:expire')]
#[Description('Perbarui status batch yang telah melewati masa kedaluwarsa dan nonaktifkan flash sale terkait')]
class ExpireBatchesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->toDateString();

        $expiredBatchIds = Batch::query()
            ->where('status', 'active')
            ->whereDate('expiry_date', '<', $today)
            ->pluck('id');

        if ($expiredBatchIds->isEmpty()) {
            $this->info('Tidak ada batch kedaluwarsa yang perlu diproses.');

            return self::SUCCESS;
        }

        $batchesCount = Batch::query()
            ->whereIn('id', $expiredBatchIds)
            ->update(['status' => 'expired']);

        $flashSalesCount = FlashSale::query()
            ->whereIn('batch_id', $expiredBatchIds)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $this->info("Berhasil memperbarui {$batchesCount} batch kedaluwarsa dan menonaktifkan {$flashSalesCount} flash sale.");

        return self::SUCCESS;
    }
}
