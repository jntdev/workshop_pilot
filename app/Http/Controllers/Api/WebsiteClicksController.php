<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteVisitsRequest;
use App\Services\Tracking\WebsiteClicksReport;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class WebsiteClicksController extends Controller
{
    public function __invoke(WebsiteVisitsRequest $request, WebsiteClicksReport $report): JsonResponse
    {
        if (! $report->isConfigured()) {
            return response()->json(['configured' => false]);
        }

        try {
            $statistics = $report->build($request->period(), CarbonImmutable::today());
        } catch (QueryException $exception) {
            report($exception);

            return response()->json(['message' => 'Impossible de joindre la base de suivi des clics.'], 503);
        }

        return response()->json(['configured' => true, ...$statistics]);
    }
}
