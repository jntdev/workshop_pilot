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
        $bikes = Bike::with(['category', 'size'])->ordered()->get();
        $categories = BikeCategory::ordered()->get();
        $sizes = BikeSize::ordered()->get();

        return Inertia::render('Bikes/Index', [
            'bikes' => $bikes,
            'categories' => $categories,
            'sizes' => $sizes,
        ]);
    }
}
