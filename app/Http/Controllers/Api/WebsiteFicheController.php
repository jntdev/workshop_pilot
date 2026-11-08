<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteVisitsRequest;
use App\Services\Tracking\WebsiteFicheReport;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class WebsiteFicheController extends Controller
{
    public function __invoke(WebsiteVisitsRequest $request, string $slug, WebsiteFicheReport $report): JsonResponse
    {
        if (! $report->isConfigured()) {
            return response()->json(['configured' => false]);
        }

        try {
            $statistics = $report->build($slug, $request->period(), CarbonImmutable::today());
        } catch (QueryException $exception) {
            report($exception);

            return response()->json(['message' => 'Impossible de joindre la base de suivi de la fiche.'], 503);
        }

        return response()->json(['configured' => true, ...$statistics]);
    }
}
