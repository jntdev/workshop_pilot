<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DemandePartenaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartenaireDemandController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $demandes = $request->user()
            ->demandes()
            ->latest()
            ->get()
            ->map(fn ($d) => $this->formatDemande($d));

        return response()->json($demandes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_debut' => ['required', 'date', 'after_or_equal:today'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'creneau' => ['required', 'string', 'in:journee,matin,apres-midi'],
            'clients' => ['required', 'array', 'min:1'],
            'clients.*.taille_cm' => ['required', 'integer', 'min:100', 'max:220'],
            'commentaire' => ['nullable', 'string', 'max:500'],
        ]);

        $demande = $request->user()->demandes()->create([
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'creneau' => $validated['creneau'],
            'velos_demandes' => $validated['clients'],
            'commentaire' => $validated['commentaire'] ?? null,
            'statut' => 'en_attente',
        ]);

        return response()->json($this->formatDemande($demande), 201);
    }

    public function show(Request $request, DemandePartenaire $demande): JsonResponse
    {
        if ($demande->partenaire_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json($this->formatDemande($demande));
    }

    private function formatDemande(DemandePartenaire $demande): array
    {
        return [
            'id' => $demande->id,
            'date_debut' => $demande->date_debut?->format('Y-m-d'),
            'date_fin' => $demande->date_fin?->format('Y-m-d'),
            'creneau' => $demande->creneau,
            'clients' => $demande->velos_demandes,
            'nb_clients' => is_array($demande->velos_demandes) ? count($demande->velos_demandes) : 0,
            'commentaire' => $demande->commentaire,
            'statut' => $demande->statut,
            'created_at' => $demande->created_at?->format('Y-m-d H:i'),
        ];
    }
}
