<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeType;
use Inertia\Inertia;
use Inertia\Response;

class BikeController extends Controller
{
    public function index(): Response
    {
        $bikes = Bike::with('bikeType')
            ->ordered()
            ->get();

        $bikeTypes = BikeType::orderBy('category')
            ->orderByRaw("FIELD(size, 'S', 'M', 'L', 'XL')")
            ->orderBy('frame_type')
            ->get();

        return Inertia::render('Bikes/Index', [
            'bikes' => $bikes,
            'bikeTypes' => $bikeTypes,
        ]);
    }
}
