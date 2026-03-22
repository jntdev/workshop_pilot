<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search', '');

        $query = Client::query()->orderBy('nom')->orderBy('prenom');

        if ($search !== '') {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(prenom) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(nom) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(telephone) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        $clients = $query->get()->map(fn (Client $client) => [
            'id' => $client->id,
            'prenom' => $client->prenom,
            'nom' => $client->nom,
            'email' => $client->email,
            'telephone' => $client->telephone,
            'adresse' => $client->adresse,
            'origine_contact' => $client->origine_contact,
            'commentaires' => $client->commentaires,
            'avantage_type' => $client->avantage_type,
            'avantage_valeur' => $client->avantage_valeur,
            'avantage_expiration' => $client->avantage_expiration?->format('Y-m-d'),
        ]);

        return response()->json($clients);
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
