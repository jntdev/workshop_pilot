<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeType;
use App\Models\ReservationItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartenaireDisponibiliteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_debut' => ['required', 'date', 'after_or_equal:today'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
        ]);

        $dateDebut = $validated['date_debut'];
        $dateFin = $validated['date_fin'];

        if (Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin)) > 30) {
            return response()->json([
                'message' => 'La plage de dates ne peut pas dépasser 30 jours.',
                'errors' => ['date_fin' => ['La plage de dates ne peut pas dépasser 30 jours.']],
            ], 422);
        }

        $agrege = [];

        foreach (BikeType::all() as $type) {
            $stockOperationnel = Bike::where('status', 'OK')
                ->whereHas('category', fn ($q) => $q->where('name', $type->category))
                ->where(function ($q) use ($type) {
                    if ($type->size) {
                        $q->whereHas('size', fn ($sq) => $sq->where('name', $type->size));
                    } else {
                        $q->whereNull('bike_size_id');
                    }
                })
                ->where(function ($q) use ($type) {
                    if ($type->frame_type) {
                        $q->where('frame_type', $type->frame_type);
                    } else {
                        $q->whereNull('frame_type');
                    }
                })
                ->count();

            if ($stockOperationnel === 0) {
                continue;
            }

            $reserve = (int) ReservationItem::where('bike_type_id', $type->id)
                ->whereHas('reservation', function ($q) use ($dateDebut, $dateFin) {
                    $q->whereNotIn('statut', ['annule'])
                        ->where('date_reservation', '<', $dateFin)
                        ->where('date_retour', '>', $dateDebut);
                })
                ->sum('quantite');

            $taille = $type->size ?? 'Sans taille';
            $categorie = $type->category;
            $cle = $categorie.'_'.$taille;

            if (! isset($agrege[$cle])) {
                $agrege[$cle] = ['categorie' => $categorie, 'taille' => $taille, 'stock_operationnel' => 0, 'reserve' => 0];
            }

            $agrege[$cle]['stock_operationnel'] += $stockOperationnel;
            $agrege[$cle]['reserve'] += $reserve;
        }

        $disponibilites = array_values(array_map(fn ($d) => [
            'categorie' => $d['categorie'],
            'taille' => $d['taille'],
            'stock_operationnel' => $d['stock_operationnel'],
            'reserve' => $d['reserve'],
            'disponible' => max(0, $d['stock_operationnel'] - $d['reserve']),
        ], $agrege));

        return response()->json([
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'disponibilites' => $disponibilites,
        ]);
    }
}
