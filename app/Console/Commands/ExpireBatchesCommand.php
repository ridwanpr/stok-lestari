<?php

namespace App\Console\Commands;

use App\Actions\ExpireBatchesAction;
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
    public function handle(ExpireBatchesAction $action): int
    {
        $result = $action->execute();

        if ($result['expired_batches'] === 0) {
            $this->info('Tidak ada batch kedaluwarsa yang perlu diproses.');

            return self::SUCCESS;
        }

        $this->info("Berhasil memperbarui {$result['expired_batches']} batch kedaluwarsa dan menonaktifkan {$result['deactivated_flash_sales']} flash sale.");

        return self::SUCCESS;
    }
}
