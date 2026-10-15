<?php

use App\Models\Article;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const CGN_PHOTO_URL = 'https://www.cgnfrance-pro.com/PartageWeb/Produits/%s_0.jpg';

    /**
     * Références CGN dont la photo catalogue est reprise sur l'article de stock correspondant.
     * Les chaînes à l'unité (« -U ») prennent la photo de la chaîne unitaire du catalogue CGN.
     *
     * @var list<array{0: string, 1: string}>
     */
    private const PHOTO_SOURCES = [
        ['535992', '535992'],
        ['538375', '538375'],
        ['538383', '538383'],
        ['505051', '505051'],
        ['505053', '505053'],
        ['FORFAIT-GAINE-CABLE-FREIN', '496264'],
        ['FORFAIT-GAINE-CABLE-DERAILLEUR', '496266'],
        ['535992-U', '535914'],
        ['538375-U', '535890'],
        ['538375 -U', '535890'],
        ['538383-U', '535891'],
        ['538383 -U', '535891'],
        ['536048-U', '535917'],
    ];

    public function up(): void
    {
        foreach (self::PHOTO_SOURCES as [$reference, $cgnReference]) {
            Article::query()
                ->where('reference', $reference)
                ->where(fn ($query) => $query->whereNull('image_url')->orWhere('image_url', ''))
                ->update(['image_url' => sprintf(self::CGN_PHOTO_URL, $cgnReference)]);
        }
    }

    public function down(): void
    {
        foreach (self::PHOTO_SOURCES as [$reference, $cgnReference]) {
            Article::query()
                ->where('reference', $reference)
                ->where('image_url', sprintf(self::CGN_PHOTO_URL, $cgnReference))
                ->update(['image_url' => null]);
        }
    }
};
