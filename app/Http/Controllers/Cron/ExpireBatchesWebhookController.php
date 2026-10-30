<?php

namespace App\Http\Controllers\Cron;

use App\Actions\ExpireBatchesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpireBatchesWebhookController extends Controller
{
    /**
     * Handle incoming cron webhook invocation.
     */
    public function __invoke(Request $request, ExpireBatchesAction $action): JsonResponse
    {
        $configuredToken = config('cron.token');

        if (blank($configuredToken)) {
            return response()->json([
                'message' => 'Cron webhook is disabled or token is not configured.',
            ], 403);
        }

        $providedToken = $request->bearerToken()
            ?? $request->header('X-Cron-Token')
            ?? $request->input('token');

        if (! is_string($providedToken) || ! hash_equals((string) $configuredToken, $providedToken)) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        $result = $action->execute();

        return response()->json([
            'message' => 'Batches expiry check completed successfully.',
            'data' => [
                'expired_batches' => $result['expired_batches'],
                'deactivated_flash_sales' => $result['deactivated_flash_sales'],
                'executed_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
