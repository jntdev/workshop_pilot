<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Données de stock créées le 2026-10-14 en local : tourets de chaînes KMC, gaines Clarks,
 * lots P2R (plaquettes, porte-patins) et forfaits atelier.
 *
 * Idempotente et non destructive : un enregistrement existant (même nom / même référence)
 * n'est ni modifié ni écrasé. Prix en centimes. Aucun mouvement de stock n'est créé.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $now = now();

        $categoryId = function (string $name, int $sort) use ($now): int {
            $row = DB::table('article_categories')->where('name', $name)->where('source', 'manual')->first();

            return $row?->id ?? DB::table('article_categories')->insertGetId([
                'name' => $name, 'source' => 'manual', 'sort_order' => $sort,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        };

        $subcategoryId = function (int $categoryId, string $name, int $sort) use ($now): int {
            $row = DB::table('article_subcategories')
                ->where('article_category_id', $categoryId)->where('name', $name)->first();

            return $row?->id ?? DB::table('article_subcategories')->insertGetId([
                'article_category_id' => $categoryId, 'name' => $name, 'sort_order' => $sort,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        };

        $named = function (string $table, string $name, int $sort) use ($now): int {
            $row = DB::table($table)->where('name', $name)->first();

            return $row?->id ?? DB::table($table)->insertGetId([
                'name' => $name, 'sort_order' => $sort, 'created_at' => $now, 'updated_at' => $now,
            ]);
        };

        $transmission = $categoryId('Transmission', 0);
        $cables = $categoryId('Câbles et gaines', 1);
        $forfaits = $categoryId('Forfaits atelier', 2);
        $freinage = $categoryId('Freinage', 3);

        $sub = [
            'chaines' => $subcategoryId($transmission, 'Chaînes', 0),
            'gaines' => $subcategoryId($cables, 'Gaines', 0),
            'gaines_cables' => $subcategoryId($forfaits, 'Gaines et câbles', 0),
            'reglages' => $subcategoryId($forfaits, 'Réglages', 1),
            'roues' => $subcategoryId($forfaits, 'Roues', 2),
            'plaquettes' => $subcategoryId($freinage, 'Plaquettes', 0),
            'patins' => $subcategoryId($freinage, 'Patins et porte-patins', 1),
        ];

        $brand = [
            'KMC' => $named('brands', 'KMC', 0),
            'Clarks' => $named('brands', 'Clarks', 1),
            'Newton' => $named('brands', 'Newton', 2),
            'Elvedes' => $named('brands', 'Elvedes', 3),
        ];
        $supplier = [
            'CGN' => $named('suppliers', 'CGN', 0),
            'P2R' => $named('suppliers', 'P2R', 1),
        ];

        $make = function (array $a) use ($now): int {
            $existing = DB::table('articles')->where('reference', $a['reference'])->first();
            if ($existing) {
                return $existing->id;
            }

            return DB::table('articles')->insertGetId($a + [
                'barcode' => null, 'lot_article_id' => null, 'lot_quantity' => null, 'brand_id' => null,
                'supplier_id' => null, 'tva_rate' => '20.00', 'notes' => null,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        };

        $lotNote = 'Lot : 40 chaînes par touret. Prix de vente du lot = prix de revient TTC (provisoire).';
        $gaineNote = "Gaine vendue à la pièce coupée : prix d'achat unitaire forfaitaire 0,90 € HT (pas de calcul au mètre). "
            ."À chaque rachat de rouleau, confirmer le nombre de gaines obtenues avec le précédent rouleau pour recalculer le prix d'achat unitaire. "
            .'Prix de vente provisoire (= prix de revient TTC).';

        // Chaînes KMC (CGN)
        $touret = [
            ['535992', 'Touret chaîne 8V KMC X8 argent/gris (50 m + 40 attaches rapides)', 28500, 34200, 0, 'Chaîne 8V KMC X8', 713, 2200],
            ['538375', 'Touret chaîne 8V renforcée KMC E8 EPT argent/gris (50 m + 40 attaches rapides)', 45900, 55080, 1, 'Chaîne 8V renforcée KMC E8 EPT', 1148, 3800],
            ['538383', 'Touret chaîne 9V renforcée KMC E9 EPT argent (50 m + 40 attaches rapides)', 59900, 71880, 2, 'Chaîne 9V renforcée KMC E9 EPT', 1498, 3800],
        ];
        foreach ($touret as [$ref, $designation, $achat, $vente, $sort, $unitDesignation, $unitAchat, $unitVente]) {
            $lotId = $make([
                'article_subcategory_id' => $sub['chaines'], 'brand_id' => $brand['KMC'], 'supplier_id' => $supplier['CGN'],
                'lot_quantity' => 40, 'reference' => $ref, 'designation' => $designation,
                'purchase_price_ht' => $achat, 'sale_price_ttc' => $vente, 'unit' => 'touret', 'notes' => $lotNote,
                'sort_order' => $sort,
            ]);
            $make([
                'article_subcategory_id' => $sub['chaines'], 'brand_id' => $brand['KMC'], 'supplier_id' => $supplier['CGN'],
                'lot_article_id' => $lotId, 'reference' => $ref.'-U', 'designation' => $unitDesignation,
                'purchase_price_ht' => $unitAchat, 'sale_price_ttc' => $unitVente, 'unit' => 'pièce',
                'sort_order' => 10 + $sort,
            ]);
        }

        // Gaines Clarks (CGN)
        $gaines = [
            ['505051', 'Rouleau gaine frein 5 mm noir Clarks (30 m)', 2195, 2634, 'Gaine frein 5 mm noir Clarks', 0],
            ['505053', 'Rouleau gaine dérailleur 4 mm noir Clarks (30 m)', 1920, 2304, 'Gaine dérailleur 4 mm noir Clarks', 2],
        ];
        foreach ($gaines as [$ref, $designation, $achat, $vente, $unitDesignation, $sort]) {
            $rouleauId = $make([
                'article_subcategory_id' => $sub['gaines'], 'brand_id' => $brand['Clarks'], 'supplier_id' => $supplier['CGN'],
                'reference' => $ref, 'designation' => $designation, 'purchase_price_ht' => $achat,
                'sale_price_ttc' => $vente, 'unit' => 'rouleau',
                'notes' => 'Rouleau de 30 m. Nombre de gaines par rouleau : à confirmer. Prix de vente provisoire.',
                'sort_order' => $sort,
            ]);
            $make([
                'article_subcategory_id' => $sub['gaines'], 'brand_id' => $brand['Clarks'], 'supplier_id' => $supplier['CGN'],
                'lot_article_id' => $rouleauId, 'reference' => $ref.'-U', 'designation' => $unitDesignation,
                'purchase_price_ht' => 90, 'sale_price_ttc' => 108, 'unit' => 'pièce', 'notes' => $gaineNote,
                'sort_order' => $sort + 1,
            ]);
        }

        // Freinage P2R : boîtes de 25 paires + paire à l'unité
        $boites = [
            [
                'plaquettes', '209009', 'Newton', 0,
                'Boîte de 25 paires plaquettes de frein VTT Shimano Deore M515-M525-M475-M495-M486 (Newton organique)',
                6098, 7318,
                'Plaquettes de frein VTT Shimano Deore M515-M525-M475-M495-M486 (Newton organique), la paire', 244, 1050,
            ],
            [
                'patins', '224698', 'Elvedes', 2,
                'Boîte de 25 paires porte-patins V-Brake à vis déporté Elvedes 72 mm noir',
                4708, 5650,
                'Porte-patins V-Brake à vis déporté Elvedes 72 mm noir, la paire', 188, 850,
            ],
        ];
        foreach ($boites as [$subKey, $ref, $brandName, $sort, $designation, $achat, $vente, $unitDesignation, $unitAchat, $unitVente]) {
            $boiteId = $make([
                'article_subcategory_id' => $sub[$subKey], 'brand_id' => $brand[$brandName], 'supplier_id' => $supplier['P2R'],
                'lot_quantity' => 25, 'reference' => $ref, 'designation' => $designation,
                'purchase_price_ht' => $achat, 'sale_price_ttc' => $vente, 'unit' => 'boîte',
                'notes' => "Réf. P2R $ref. Boîte de 25 paires. Prix de vente provisoire.", 'sort_order' => $sort,
            ]);
            $make([
                'article_subcategory_id' => $sub[$subKey], 'brand_id' => $brand[$brandName], 'supplier_id' => $supplier['P2R'],
                'lot_article_id' => $boiteId, 'reference' => $ref.'-U', 'designation' => $unitDesignation,
                'purchase_price_ht' => $unitAchat, 'sale_price_ttc' => $unitVente, 'unit' => 'paire',
                'sort_order' => $sort + 1,
            ]);
        }

        // Forfaits atelier (prix TTC fixes ; purchase_price_ht = coût matière, 0 si main d'œuvre seule)
        $mo = "Forfait main d'œuvre, prix TTC fixe.";
        $forfait = [
            ['gaines_cables', 'FORFAIT-GAINE-CABLE-FREIN', 'Forfait remplacement gaine + câble de frein', 189, 2000, 'Forfait 20 € TTC. Coût matière : gaine 0,90 € HT + câble frein inox Clarks 0,99 € HT.', 0],
            ['gaines_cables', 'FORFAIT-GAINE-CABLE-DERAILLEUR', 'Forfait remplacement gaine + câble de dérailleur', 163, 2000, 'Forfait 20 € TTC. Coût matière : gaine 0,90 € HT + câble dérailleur inox Clarks 0,73 € HT.', 0],
            ['reglages', 'FORFAIT-REGLAGE-FREIN', 'Forfait réglage de frein', 0, 1300, $mo, 1],
            ['reglages', 'FORFAIT-REGLAGE-DERAILLEUR', 'Forfait réglage de dérailleur', 0, 1300, $mo, 2],
            ['roues', 'FORFAIT-DEVOILAGE', 'Forfait dévoilage de roue', 0, 1800, $mo, 3],
            ['roues', 'FORFAIT-CHAMBRE-PNEU', 'Forfait remplacement chambre à air ou pneu', 0, 1300, $mo, 4],
            ['roues', 'FORFAIT-PURGE-HYDRAULIQUE', 'Forfait purge de frein hydraulique (à partir de)', 0, 3300, "Forfait main d'oeuvre, à partir de 33 € TTC (prix client, à ajuster au cas par cas).", 6],
        ];
        foreach ($forfait as [$subKey, $ref, $designation, $cout, $vente, $notes, $sort]) {
            $make([
                'article_subcategory_id' => $sub[$subKey], 'reference' => $ref, 'designation' => $designation,
                'purchase_price_ht' => $cout, 'sale_price_ttc' => $vente, 'unit' => 'forfait', 'notes' => $notes,
                'sort_order' => $sort,
            ]);
        }
    }

    public function down(): void
    {
        // Volontairement vide : ces données peuvent avoir été utilisées dans des devis ; pas de suppression automatique.
    }
};
