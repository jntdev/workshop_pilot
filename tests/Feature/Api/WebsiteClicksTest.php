<?php

namespace Tests\Feature\Api;

use App\Models\ClicSite;
use App\Models\ParcoursSite;
use App\Models\User;
use App\Models\VisiteFiche;
use App\Models\VisiteSource;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * La base de suivi est simulée en SQLite mémoire et l'utilisateur n'est pas enregistré :
 * ces tests ne touchent jamais la base de l'application.
 */
class WebsiteClicksTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.tracking' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge('tracking');

        Schema::connection('tracking')->create('clics_site', function (Blueprint $table) {
            $table->id();
            $table->date('jour');
            $table->string('categorie', 20);
            $table->string('cle', 60);
            $table->string('contexte', 120)->default('');
            $table->unsignedInteger('visiteurs')->default(0);
            $table->unsignedInteger('clics')->default(0);
            $table->timestamps();
            $table->unique(['jour', 'categorie', 'cle', 'contexte']);
        });
        Schema::connection('tracking')->create('visites_fiches', function (Blueprint $table) {
            $table->id();
            $table->date('jour');
            $table->string('slug', 120);
            $table->string('marque', 40);
            $table->boolean('promo_blackfriday')->default(false);
            $table->boolean('visible_magasin')->default(false);
            $table->unsignedInteger('visiteurs')->default(0);
            $table->unsignedInteger('vues')->default(0);
            $table->timestamps();
            $table->unique(['jour', 'slug']);
        });
        Schema::connection('tracking')->create('parcours_site', function (Blueprint $table) {
            $table->id();
            $table->date('jour');
            $table->string('parcours', 30);
            $table->unsignedTinyInteger('rang');
            $table->string('etape', 120);
            $table->unsignedInteger('visites')->default(0);
            $table->timestamps();
            $table->unique(['jour', 'parcours', 'rang', 'etape']);
        });
        Schema::connection('tracking')->create('visites_sources', function (Blueprint $table) {
            $table->id();
            $table->date('jour');
            $table->string('cible', 120);
            $table->string('source', 20);
            $table->unsignedInteger('visiteurs')->default(0);
            $table->unsignedInteger('vues')->default(0);
            $table->timestamps();
            $table->unique(['jour', 'cible', 'source']);
        });
        Schema::connection('tracking')->create('demandes_recontact', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120);
            $table->timestamps();
        });

        $this->travelTo(CarbonImmutable::parse('2026-11-20 15:00:00'));
        $this->user = User::factory()->make();
    }

    private function recordClicks(string $jour, string $categorie, string $cle, int $visiteurs, int $clics, string $contexte = ''): void
    {
        ClicSite::factory()->create([
            'jour' => $jour,
            'categorie' => $categorie,
            'cle' => $cle,
            'contexte' => $contexte,
            'visiteurs' => $visiteurs,
            'clics' => $clics,
        ]);
    }

    /**
     * @param  array<string, bool>  $flags
     */
    private function recordVisits(string $jour, string $slug, int $visiteurs, int $vues, array $flags = []): void
    {
        VisiteFiche::factory()->create([
            'jour' => $jour,
            'slug' => $slug,
            'marque' => Str::before($slug, '-'),
            'visiteurs' => $visiteurs,
            'vues' => $vues,
            ...$flags,
        ]);
    }

    private function recordTunnelStep(string $jour, string $parcours, int $rang, string $etape, int $visites): void
    {
        ParcoursSite::factory()->create(['jour' => $jour, 'parcours' => $parcours, 'rang' => $rang, 'etape' => $etape, 'visites' => $visites]);
    }

    private function recordSource(string $jour, string $cible, string $source, int $visiteurs, int $vues): void
    {
        VisiteSource::factory()->create(['jour' => $jour, 'cible' => $cible, 'source' => $source, 'visiteurs' => $visiteurs, 'vues' => $vues]);
    }

    private function recordRequest(string $slug, string $date): void
    {
        DB::connection('tracking')->table('demandes_recontact')->insert(['slug' => $slug, 'created_at' => $date, 'updated_at' => $date]);
    }

    #[Test]
    public function it_gives_each_home_button_a_click_rate_over_the_home_visitors(): void
    {
        $this->recordClicks('2026-11-18', 'page', 'accueil', 80, 100);
        $this->recordClicks('2026-11-19', 'page', 'accueil', 20, 25);
        $this->recordClicks('2026-11-18', 'accueil', 'acheter', 30, 35);
        $this->recordClicks('2026-11-19', 'accueil', 'blackfriday', 10, 12);
        $this->recordClicks('2026-09-01', 'accueil', 'louer', 99, 99);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d');

        $response->assertOk()
            ->assertJsonPath('configured', true)
            ->assertJsonPath('has_data', true)
            ->assertJsonPath('tracked_since', '2026-09-01')
            ->assertJsonPath('home.visitors', 100)
            ->assertJsonPath('home.views', 125)
            ->assertJsonPath('home.buttons.0.key', 'acheter')
            ->assertJsonPath('home.buttons.0.visitors', 30)
            ->assertJsonPath('home.buttons.0.clicks', 35)
            ->assertJsonPath('home.buttons.2.visitors', 0)
            ->assertJsonPath('home.buttons.3.visitors', 10);
        $this->assertEqualsWithDelta(30.0, $response->json('home.buttons.0.rate'), 0.001);
        $this->assertEqualsWithDelta(10.0, $response->json('home.buttons.3.rate'), 0.001);
    }

    #[Test]
    public function it_has_no_click_rate_without_home_visitors(): void
    {
        $this->recordClicks('2026-11-19', 'accueil', 'acheter', 3, 3);

        $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d')
            ->assertOk()
            ->assertJsonPath('home.buttons.0.rate', null);
    }

    #[Test]
    public function it_ranks_the_brand_filters_by_catalogue(): void
    {
        $this->recordClicks('2026-11-19', 'filtre', 'ncm', 5, 6, 'loisir');
        $this->recordClicks('2026-11-19', 'filtre', 'neomouv', 8, 9, 'loisir');
        $this->recordClicks('2026-11-19', 'filtre', 'blackfriday', 4, 4, 'loisir');
        $this->recordClicks('2026-11-19', 'filtre', 'mmr', 2, 2, 'sport');

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d');

        $response->assertOk();
        $this->assertSame(
            [['loisir', 'neomouv', 9], ['loisir', 'ncm', 6], ['loisir', 'blackfriday', 4], ['sport', 'mmr', 2]],
            collect($response->json('filters'))->map(fn (array $row) => [$row['univers'], $row['key'], $row['clicks']])->all(),
        );
    }

    #[Test]
    public function it_lists_the_black_friday_fiches_with_the_model_of_each_bike(): void
    {
        $this->recordClicks('2026-11-19', 'page', 'accueil', 100, 120);
        $this->recordVisits('2026-11-19', 'ncm-cg7', 12, 20, ['promo_blackfriday' => true]);
        $this->recordVisits('2026-11-19', 'neomouv-elaia-3', 8, 9, ['promo_blackfriday' => true]);
        $this->recordVisits('2026-11-19', 'mmr-rakish', 50, 60);

        $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d')
            ->assertOk()
            ->assertJsonPath('blackfriday.fiches', [
                ['slug' => 'ncm-cg7', 'marque' => 'ncm', 'visitors' => 12, 'views' => 20],
                ['slug' => 'neomouv-elaia-3', 'marque' => 'neomouv', 'visitors' => 8, 'views' => 9],
            ]);
    }

    #[Test]
    public function it_sums_each_tunnel_step_over_the_period_in_order(): void
    {
        $this->recordClicks('2026-11-18', 'page', 'accueil', 1, 1);
        foreach (['2026-11-18' => [50, 10, 4], '2026-11-19' => [30, 6, 1], '2026-09-01' => [999, 999, 999]] as $jour => $visites) {
            foreach (['accueil', 'bouton_blackfriday', 'fiche_blackfriday'] as $rang => $etape) {
                $this->recordTunnelStep($jour, 'blackfriday', $rang, $etape, $visites[$rang]);
            }
        }
        $this->recordTunnelStep('2026-11-19', 'magasin', 0, 'fiche_magasin', 7);

        $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d')
            ->assertOk()
            ->assertJsonPath('tunnels', [
                ['key' => 'blackfriday', 'steps' => [
                    ['step' => 'accueil', 'visits' => 80],
                    ['step' => 'bouton_blackfriday', 'visits' => 16],
                    ['step' => 'fiche_blackfriday', 'visits' => 5],
                ]],
                ['key' => 'magasin', 'steps' => [['step' => 'fiche_magasin', 'visits' => 7]]],
            ]);
    }

    #[Test]
    public function it_ranks_the_entry_steps_without_mixing_them_with_the_tunnels(): void
    {
        $this->recordClicks('2026-11-19', 'page', 'accueil', 1, 1);
        $this->recordTunnelStep('2026-11-18', ParcoursSite::ENTRY_PARCOURS, 0, 'accueil', 40);
        $this->recordTunnelStep('2026-11-19', ParcoursSite::ENTRY_PARCOURS, 0, 'accueil', 20);
        $this->recordTunnelStep('2026-11-19', ParcoursSite::ENTRY_PARCOURS, 0, 'fiche', 25);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d');

        $response->assertOk()
            ->assertJsonPath('entries', [['step' => 'fiche', 'visits' => 25], ['step' => 'accueil', 'visits' => 20]])
            ->assertJsonPath('tunnels', []);
    }

    #[Test]
    public function it_shows_where_the_visits_come_from_for_the_home_page_and_for_the_fiches(): void
    {
        $this->recordClicks('2026-11-18', 'page', 'accueil', 1, 1);
        $this->recordVisits('2026-11-18', 'neomouv-cronos-3', 12, 15);
        $this->recordVisits('2026-11-19', 'neomouv-cronos-3', 8, 9);
        $this->recordVisits('2026-11-19', 'mmr-rakish', 5, 5);
        $this->recordSource('2026-11-18', 'accueil', 'direct', 6, 7);
        $this->recordSource('2026-11-19', 'accueil', 'moteur', 3, 3);
        $this->recordSource('2026-11-18', 'neomouv-cronos-3', 'moteur', 9, 11);
        $this->recordSource('2026-11-19', 'neomouv-cronos-3', 'moteur', 6, 7);
        $this->recordSource('2026-11-19', 'neomouv-cronos-3', 'qr', 2, 2);
        $this->recordSource('2026-11-19', 'mmr-rakish', 'direct', 5, 5);
        $this->recordSource('2026-09-01', 'neomouv-cronos-3', 'direct', 99, 99);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d');

        $response->assertOk()
            ->assertJsonPath('sources.home', [['source' => 'direct', 'visitors' => 6, 'views' => 7], ['source' => 'moteur', 'visitors' => 3, 'views' => 3]])
            ->assertJsonPath('sources.fiches', [
                ['source' => 'moteur', 'visitors' => 15, 'views' => 18],
                ['source' => 'direct', 'visitors' => 5, 'views' => 5],
                ['source' => 'qr', 'visitors' => 2, 'views' => 2],
            ])
            ->assertJsonPath('sources.top_fiches.0', [
                'slug' => 'neomouv-cronos-3',
                'marque' => 'neomouv',
                'visitors' => 17,
                'sources' => [['source' => 'moteur', 'visitors' => 15], ['source' => 'qr', 'visitors' => 2]],
            ])
            ->assertJsonPath('sources.top_fiches.1.slug', 'mmr-rakish');
    }

    #[Test]
    public function it_has_no_sources_before_any_visit_origin_is_tracked(): void
    {
        $this->recordClicks('2026-11-19', 'page', 'accueil', 1, 1);

        $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d')
            ->assertOk()
            ->assertJsonPath('sources', ['home' => [], 'fiches' => [], 'top_fiches' => []]);
    }

    #[Test]
    public function it_compares_the_fiches_visible_in_store_with_the_others(): void
    {
        $this->recordClicks('2026-11-18', 'page', 'accueil', 1, 1);
        $this->recordVisits('2026-11-18', 'ncm-cg7', 10, 12, ['visible_magasin' => true]);
        $this->recordVisits('2026-11-19', 'ncm-cg7', 10, 12, ['visible_magasin' => true]);
        $this->recordVisits('2026-11-19', 'ncm-t3s', 20, 30, ['visible_magasin' => true]);
        $this->recordVisits('2026-11-19', 'mmr-rakish', 6, 8);
        $this->recordRequest('ncm-cg7', '2026-11-19 10:00:00');
        $this->recordRequest('ncm-t3s', '2026-11-19 10:00:00');
        $this->recordRequest('ncm-t3s', '2026-10-01 10:00:00');
        $this->recordRequest('mmr-rakish', '2026-11-19 10:00:00');

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d');

        $response->assertOk()
            ->assertJsonPath('magasin.with.fiches', 2)
            ->assertJsonPath('magasin.with.visitors', 40)
            ->assertJsonPath('magasin.with.requests', 2)
            ->assertJsonPath('magasin.without.fiches', 1)
            ->assertJsonPath('magasin.without.visitors', 6)
            ->assertJsonPath('magasin.without.requests', 1);
        $this->assertEqualsWithDelta(20.0, $response->json('magasin.with.visitors_per_fiche'), 0.001);
        $this->assertEqualsWithDelta(5.0, $response->json('magasin.with.requests_per_100_visitors'), 0.001);
    }

    #[Test]
    public function it_has_no_ratio_for_fiches_without_visitors(): void
    {
        $this->recordClicks('2026-11-19', 'page', 'accueil', 1, 1);

        $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d')
            ->assertOk()
            ->assertJsonPath('magasin.with.visitors_per_fiche', null)
            ->assertJsonPath('magasin.with.requests_per_100_visitors', null)
            ->assertJsonPath('tunnels', [])
            ->assertJsonPath('entries', []);
    }

    #[Test]
    public function it_lists_contacts_by_type_and_by_place_in_the_site(): void
    {
        $this->recordClicks('2026-11-19', 'contact', 'telephone', 7, 9, 'accueil/pied_de_page');
        $this->recordClicks('2026-11-19', 'contact', 'telephone', 3, 3, 'atelier/menu');
        $this->recordClicks('2026-11-19', 'contact', 'email', 2, 2, 'atelier/menu');
        $this->recordClicks('2026-11-19', 'contact', 'itineraire', 4, 4, 'accueil/nous-trouver');

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d');

        $response->assertOk()
            ->assertJsonPath('contacts.types', [
                ['key' => 'telephone', 'visitors' => 10, 'clicks' => 12],
                ['key' => 'email', 'visitors' => 2, 'clicks' => 2],
                ['key' => 'itineraire', 'visitors' => 4, 'clicks' => 4],
            ])
            ->assertJsonPath('contacts.places.0', ['page' => 'accueil', 'zone' => 'pied_de_page', 'key' => 'telephone', 'visitors' => 7, 'clicks' => 9]);
        $this->assertCount(4, $response->json('contacts.places'));
    }

    #[Test]
    public function it_ranks_the_colours_with_card_and_fiche_clicks(): void
    {
        $this->recordClicks('2026-11-19', 'couleur_carte', 'Bordeaux', 4, 5, 'neomouv-linaria-ng');
        $this->recordClicks('2026-11-19', 'couleur_fiche', 'Bordeaux', 2, 3, 'neomouv-linaria-ng');
        $this->recordClicks('2026-11-19', 'couleur_carte', 'Sable', 1, 1, 'neomouv-linaria-ng');
        $this->recordClicks('2026-11-19', 'couleur_fiche', 'Noir', 3, 3, 'ncm-cg7');

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks?period=7d');

        $response->assertOk()
            ->assertJsonPath('colors.0', ['slug' => 'neomouv-linaria-ng', 'marque' => 'neomouv', 'couleur' => 'Bordeaux', 'card_clicks' => 5, 'fiche_clicks' => 3, 'visitors' => 6])
            ->assertJsonPath('colors.1.couleur', 'Noir')
            ->assertJsonPath('colors.2.couleur', 'Sable');
    }

    #[Test]
    public function it_reports_no_data_before_any_click_is_tracked(): void
    {
        $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks')
            ->assertOk()
            ->assertJson(['configured' => true, 'has_data' => false]);
    }

    #[Test]
    public function it_reports_an_unconfigured_tracking_database(): void
    {
        config(['database.connections.tracking.database' => null]);

        $this->actingAs($this->user)->getJson('/api/dashboard/website-clicks')
            ->assertOk()
            ->assertExactJson(['configured' => false]);
    }

    #[Test]
    public function it_reports_an_unreachable_tracking_database(): void
    {
        Exceptions::fake();
        config(['database.connections.tracking' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 1,
            'database' => 'tracking',
            'username' => 'tracking',
            'password' => '',
        ]]);
        DB::purge('tracking');

        $this->actingAs($this->user)
            ->getJson('/api/dashboard/website-clicks')
            ->assertStatus(503)
            ->assertJsonPath('message', 'Impossible de joindre la base de suivi des clics.');

        Exceptions::assertReported(QueryException::class);
    }

    #[Test]
    public function it_rejects_an_unknown_period(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/dashboard/website-clicks?period=2y')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['period' => 'La période demandée est invalide.']);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->getJson('/api/dashboard/website-clicks')->assertUnauthorized();
    }
}
