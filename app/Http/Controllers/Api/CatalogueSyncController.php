<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Catalogue\CgnFtpSyncService;
use Illuminate\Http\JsonResponse;

class CatalogueSyncController extends Controller
{
    public function syncCgn(CgnFtpSyncService $service): JsonResponse
    {
        $result = $service->sync();

        return response()->json($result, $result['success'] ? 200 : 502);
    }
}
