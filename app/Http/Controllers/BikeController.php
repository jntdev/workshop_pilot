<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use Inertia\Inertia;
use Inertia\Response;

class BikeController extends Controller
{
    public function index(): Response
    {
        $bikes = Bike::ordered()->get();

        return Inertia::render('Bikes/Index', [
            'bikes' => $bikes,
        ]);
    }
}
