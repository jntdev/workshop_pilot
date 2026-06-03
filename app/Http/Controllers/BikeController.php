<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeCategory;
use App\Models\BikeSize;
use Inertia\Inertia;
use Inertia\Response;

class BikeController extends Controller
{
    public function index(): Response
    {
        $bikes = Bike::with(['category', 'size'])
            ->ordered()
            ->addSelect([
                'pending_maintenance_count' => \App\Models\BikeMaintenanceLog::selectRaw('COUNT(*)')
                    ->whereColumn('bike_id', 'bikes.id')
                    ->where('status', 'todo'),
            ])
            ->get();
        $categories = BikeCategory::ordered()->get();
        $sizes = BikeSize::ordered()->get();

        return Inertia::render('Bikes/Index', [
            'bikes' => $bikes,
            'categories' => $categories,
            'sizes' => $sizes,
        ]);
    }

    public function show(Bike $bike): Response
    {
        $bike->load(['category', 'size', 'maintenanceLogs']);

        return Inertia::render('Bikes/Show', [
            'bike' => $bike,
        ]);
    }
}
