<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedEmail;
use App\Models\Partenaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PartenaireAuthController extends Controller
{
    public function inscription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:partenaires,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $emailAutorise = AuthorizedEmail::where('email', strtolower($validated['email']))
            ->where('type', 'partenaire')
            ->exists();

        if (! $emailAutorise) {
            throw ValidationException::withMessages([
                'email' => ['Cette adresse email n\'est pas autorisée à créer un compte.'],
            ]);
        }

        $partenaire = Partenaire::create([
            'nom' => $validated['nom'],
            'email' => strtolower($validated['email']),
            'password' => $validated['password'],
            'actif' => true,
        ]);

        $token = $partenaire->createToken('partenaire-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'partenaire' => [
                'id' => $partenaire->id,
                'nom' => $partenaire->nom,
                'email' => $partenaire->email,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $partenaire = Partenaire::where('email', strtolower($validated['email']))->first();

        if (! $partenaire || ! Hash::check($validated['password'], $partenaire->password)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        if (! $partenaire->actif) {
            return response()->json(['message' => 'Compte désactivé.'], 403);
        }

        $token = $partenaire->createToken('partenaire-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'partenaire' => [
                'id' => $partenaire->id,
                'nom' => $partenaire->nom,
                'email' => $partenaire->email,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }

    public function me(Request $request): JsonResponse
    {
        $partenaire = $request->user();

        return response()->json([
            'id' => $partenaire->id,
            'nom' => $partenaire->nom,
            'email' => $partenaire->email,
        ]);
    }
}
