<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedEmail;
use App\Models\Partenaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PartenaireController extends Controller
{
    public function index(): Response
    {
        $whitelist = AuthorizedEmail::where('type', 'partenaire')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'email', 'created_at']);

        $comptes = Partenaire::orderBy('created_at', 'desc')
            ->get(['id', 'nom', 'email', 'actif', 'created_at']);

        $emailsInscrits = $comptes->pluck('email')->map(fn ($e) => strtolower($e))->toArray();

        $enAttente = $whitelist->filter(
            fn ($w) => ! in_array(strtolower($w->email), $emailsInscrits)
        )->values();

        return Inertia::render('Admin/Partenaires/Index', [
            'enAttente' => $enAttente,
            'comptes' => $comptes,
        ]);
    }

    public function whitelist(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:authorized_emails,email'],
        ]);

        AuthorizedEmail::create([
            'email' => strtolower($validated['email']),
            'type' => 'partenaire',
        ]);

        return response()->json(['message' => 'Email autorisé.'], 201);
    }

    public function removeWhitelist(string $email): JsonResponse
    {
        $entry = AuthorizedEmail::where('email', strtolower($email))
            ->where('type', 'partenaire')
            ->firstOrFail();

        $alreadyRegistered = Partenaire::where('email', strtolower($email))->exists();

        if ($alreadyRegistered) {
            return response()->json([
                'message' => 'Ce partenaire a déjà créé son compte. Désactivez le compte plutôt que de retirer l\'email.',
            ], 422);
        }

        $entry->delete();

        return response()->json(['message' => 'Email retiré de la whitelist.']);
    }

    public function toggle(Partenaire $partenaire): JsonResponse
    {
        $partenaire->update(['actif' => ! $partenaire->actif]);

        return response()->json([
            'actif' => $partenaire->actif,
            'message' => $partenaire->actif ? 'Compte activé.' : 'Compte désactivé.',
        ]);
    }
}
