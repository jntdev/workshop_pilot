<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(): JsonResponse
    {
        $clients = Client::all();

        return response()->json([
            'data' => $clients,
        ]);
    }

    /**
     * Display the specified client.
     */
    public function show(string $id): JsonResponse
    {
        $client = Client::find($id);

        if (! $client) {
            return response()->json([
                'message' => 'Client not found',
            ], 404);
        }

        return response()->json([
            'data' => $client,
        ]);
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return response()->json([
            'data' => $client,
        ], 201);
    }

    /**
     * Update the specified client in storage.
     */
    public function update(UpdateClientRequest $request, string $id): JsonResponse
    {
        $client = Client::find($id);

        if (! $client) {
            return response()->json([
                'message' => 'Client not found',
            ], 404);
        }

        $client->update($request->validated());

        return response()->json([
            'data' => $client,
        ]);
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $client = Client::find($id);

        if (! $client) {
            return response()->json([
                'message' => 'Client not found',
            ], 404);
        }

        $client->delete();

        return response()->json(null, 204);
    }
}
