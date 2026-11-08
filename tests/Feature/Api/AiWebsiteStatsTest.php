<?php

namespace Tests\Feature\Api;

use App\Models\ClicSite;
use App\Models\VisiteFiche;
use App\Models\VisiteSource;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * La base de suivi est simulée en SQLite mémoire : ces tests ne touchent jamais la base de l'application.
 */
class AiWebsiteStatsTest extends TestCase
{
    private const TOKEN = 'secret-ai-token';

    protected function setUp(): void
    {
        parent::setUp();

        config(['ai.token' => self::TOKEN, 'database.connections.tracking' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge('tracking');

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
        Schema::connection('tracking')->create('parcours_site', function (Blueprint $table) {
            $table->id();
            $table->date('jour');
            $table->string('parcours', 30);
            $table->unsignedTinyInteger('rang');
            $table->string('etape', 120);
            $table->unsignedInteger('visites')->default(0);
            $table->timestamps();
        });
        Schema::connection('tracking')->create('visites_sources', function (Blueprint $table) {
            $table->id();
            $table->date('jour');
            $table->string('cible', 120);
            $table->string('source', 20);
            $table->unsignedInteger('visiteurs')->default(0);
            $table->unsignedInteger('vues')->default(0);
            $table->timestamps();
        });
        Schema::connection('tracking')->create('demandes_recontact', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120);
            $table->timestamps();
        });

        $this->travelTo(CarbonImmutable::parse('2026-11-20 15:00:00'));
    }

    /**
     * @return array<string, string>
     */
    private function auth(): array
    {
        return ['Authorization' => 'Bearer '.self::TOKEN];
    }

    #[Test]
    public function it_requires_the_ai_token_for_the_website_statistics(): void
    {
        $this->getJson('/api/ai/website/visits')->assertUnauthorized();
        $this->getJson('/api/ai/website/clicks')->assertUnauthorized();
        $this->getJson('/api/ai/website/clicks', ['Authorization' => 'Bearer nope'])->assertUnauthorized();
    }

    #[Test]
    public function it_serves_the_visits_statistics_like_the_dashboard(): void
    {
        VisiteFiche::factory()->create(['jour' => '2026-11-19', 'slug' => 'ncm-cg7', 'marque' => 'ncm', 'visiteurs' => 5, 'vues' => 8]);

        $this->getJson('/api/ai/website/visits?period=7d', $this->auth())
            ->assertOk()
            ->assertJsonPath('configured', true)
            ->assertJsonPath('kpis.visitors.value', 5)
            ->assertJsonPath('fiches.0.slug', 'ncm-cg7');
    }

    #[Test]
    public function it_serves_the_clicks_statistics_like_the_dashboard(): void
    {
        ClicSite::factory()->create(['jour' => '2026-11-19', 'categorie' => 'page', 'cle' => 'accueil', 'contexte' => '', 'visiteurs' => 10, 'clics' => 12]);
        ClicSite::factory()->create(['jour' => '2026-11-19', 'categorie' => 'accueil', 'cle' => 'acheter', 'contexte' => '', 'visiteurs' => 4, 'clics' => 4]);

        $this->getJson('/api/ai/website/clicks?period=7d', $this->auth())
            ->assertOk()
            ->assertJsonPath('home.visitors', 10)
            ->assertJsonPath('home.buttons.0.visitors', 4);
    }

    #[Test]
    public function it_validates_the_period_and_stays_read_only(): void
    {
        $this->getJson('/api/ai/website/clicks?period=2y', $this->auth())->assertUnprocessable();
        $this->postJson('/api/ai/website/clicks', [], $this->auth())->assertStatus(405);
        $this->deleteJson('/api/ai/website/visits', [], $this->auth())->assertStatus(405);
    }

    #[Test]
    public function it_serves_the_daily_visits_and_the_origin_of_one_fiche(): void
    {
        VisiteFiche::factory()->create(['jour' => '2026-11-18', 'slug' => 'cronos-3', 'marque' => 'neomouv', 'visiteurs' => 4, 'vues' => 5]);
        VisiteFiche::factory()->create(['jour' => '2026-11-20', 'slug' => 'cronos-3', 'marque' => 'neomouv', 'visiteurs' => 9, 'vues' => 11, 'visible_magasin' => true]);
        VisiteFiche::factory()->create(['jour' => '2026-11-20', 'slug' => 'elaia-3', 'marque' => 'neomouv', 'visiteurs' => 50, 'vues' => 50]);
        VisiteSource::factory()->create(['jour' => '2026-11-20', 'cible' => 'cronos-3', 'source' => 'moteur', 'visiteurs' => 7, 'vues' => 8]);
        VisiteSource::factory()->create(['jour' => '2026-11-20', 'cible' => 'cronos-3', 'source' => 'qr', 'visiteurs' => 2, 'vues' => 3]);
        VisiteSource::factory()->create(['jour' => '2026-11-20', 'cible' => 'elaia-3', 'source' => 'direct', 'visiteurs' => 50, 'vues' => 50]);

        $response = $this->getJson('/api/ai/website/fiches/cronos-3?period=7d', $this->auth());

        $response->assertOk()
            ->assertJsonPath('has_data', true)
            ->assertJsonPath('slug', 'cronos-3')
            ->assertJsonPath('marque', 'neomouv')
            ->assertJsonPath('totals', ['visitors' => 13, 'views' => 16])
            ->assertJsonPath('sources', [['source' => 'moteur', 'visitors' => 7, 'views' => 8], ['source' => 'qr', 'visitors' => 2, 'views' => 3]])
            ->assertJsonPath('visible_magasin', true)
            ->assertJsonPath('promo_blackfriday', false);
        $this->assertSame(['2026-11-18' => 4, '2026-11-19' => 0, '2026-11-20' => 9], collect($response->json('series'))->pluck('visitors', 'date')->all());
    }

    #[Test]
    public function it_reports_no_data_for_an_unknown_fiche_and_requires_the_token(): void
    {
        $this->getJson('/api/ai/website/fiches/inconnue', $this->auth())->assertOk()->assertJsonPath('has_data', false);
        $this->getJson('/api/ai/website/fiches/cronos-3')->assertUnauthorized();
        $this->getJson('/api/ai/website/fiches/Pas Valable', $this->auth())->assertNotFound();
    }
}
