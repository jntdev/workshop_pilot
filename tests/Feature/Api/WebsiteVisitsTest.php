<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\VisiteFiche;
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
class WebsiteVisitsTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.tracking' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge('tracking');

        Schema::connection('tracking')->create('visites_fiches', function (Blueprint $table) {
            $table->id();
            $table->date('jour');
            $table->string('slug', 120);
            $table->string('marque', 40);
            $table->unsignedInteger('visiteurs')->default(0);
            $table->unsignedInteger('vues')->default(0);
            $table->timestamps();
            $table->unique(['jour', 'slug']);
        });

        $this->travelTo(CarbonImmutable::parse('2026-10-10 15:00:00'));
        $this->user = User::factory()->make();
    }

    private function recordVisits(string $jour, string $slug, int $visiteurs, int $vues): void
    {
        VisiteFiche::factory()->create([
            'jour' => $jour,
            'slug' => $slug,
            'marque' => Str::before($slug, '-'),
            'visiteurs' => $visiteurs,
            'vues' => $vues,
        ]);
    }

    #[Test]
    public function it_summarises_the_period_with_trends_against_the_previous_period(): void
    {
        $this->recordVisits('2026-09-26', 'mmr-rakish', 100, 100);
        $this->recordVisits('2026-09-27', 'adris-vadrouille', 10, 20);
        $this->recordVisits('2026-10-03', 'mmr-rakish', 6, 10);
        $this->recordVisits('2026-10-04', 'mmr-rakish', 10, 15);
        $this->recordVisits('2026-10-10', 'mmr-rakish', 5, 10);
        $this->recordVisits('2026-10-10', 'ncm-munich', 5, 5);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-visits?period=7d');

        $response->assertOk()
            ->assertJsonPath('configured', true)
            ->assertJsonPath('has_data', true)
            ->assertJsonPath('tracked_since', '2026-09-26')
            ->assertJsonPath('range', ['start' => '2026-10-04', 'end' => '2026-10-10', 'granularity' => 'day'])
            ->assertJsonPath('kpis.visitors.value', 20)
            ->assertJsonPath('kpis.views.value', 30)
            ->assertJsonPath('kpis.fiches.value', 2);

        $this->assertEqualsWithDelta(25.0, $response->json('kpis.visitors.trend'), 0.001);
        $this->assertEqualsWithDelta(0.0, $response->json('kpis.views.trend'), 0.001);
        $this->assertEqualsWithDelta(1.5, $response->json('kpis.views_per_visitor.value'), 0.001);
        $this->assertEqualsWithDelta(-20.0, $response->json('kpis.views_per_visitor.trend'), 0.001);
        $this->assertEqualsWithDelta(0.0, $response->json('kpis.fiches.trend'), 0.001);
    }

    #[Test]
    public function it_has_no_trend_when_the_previous_period_predates_tracking(): void
    {
        $this->recordVisits('2026-10-01', 'mmr-rakish', 4, 4);
        $this->recordVisits('2026-10-08', 'mmr-rakish', 8, 8);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-visits?period=7d');

        $response->assertOk()
            ->assertJsonPath('kpis.visitors.value', 8)
            ->assertJsonPath('kpis.visitors.trend', null)
            ->assertJsonPath('kpis.views.trend', null)
            ->assertJsonPath('kpis.views_per_visitor.trend', null)
            ->assertJsonPath('kpis.fiches.trend', null);
    }

    #[Test]
    public function it_returns_one_point_per_day_including_days_without_visits(): void
    {
        $this->recordVisits('2026-10-04', 'mmr-rakish', 3, 4);
        $this->recordVisits('2026-10-04', 'ncm-munich', 2, 2);
        $this->recordVisits('2026-10-06', 'mmr-rakish', 1, 1);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-visits?period=7d');

        $response->assertOk()
            ->assertJsonCount(7, 'series')
            ->assertJsonPath('series.0', ['date' => '2026-10-04', 'visitors' => 5, 'views' => 6])
            ->assertJsonPath('series.1', ['date' => '2026-10-05', 'visitors' => 0, 'views' => 0])
            ->assertJsonPath('series.2', ['date' => '2026-10-06', 'visitors' => 1, 'views' => 1])
            ->assertJsonPath('series.6.date', '2026-10-10');
    }

    #[Test]
    public function it_groups_points_by_week_from_the_first_tracked_week_for_long_periods(): void
    {
        $this->recordVisits('2026-09-29', 'mmr-rakish', 1, 1);
        $this->recordVisits('2026-10-04', 'mmr-rakish', 4, 4);
        $this->recordVisits('2026-10-05', 'mmr-rakish', 2, 3);
        $this->recordVisits('2026-10-10', 'mmr-rakish', 1, 1);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-visits?period=12m');

        $response->assertOk()
            ->assertJsonPath('range', ['start' => '2026-09-28', 'end' => '2026-10-10', 'granularity' => 'week'])
            ->assertJsonPath('series', [
                ['date' => '2026-09-28', 'visitors' => 5, 'views' => 5],
                ['date' => '2026-10-05', 'visitors' => 3, 'views' => 4],
            ])
            ->assertJsonPath('kpis.visitors.trend', null);
    }

    #[Test]
    public function it_ranks_brands_and_fiches_by_visits_then_views(): void
    {
        $this->recordVisits('2026-10-01', 'mmr-rakish', 10, 12);
        $this->recordVisits('2026-10-02', 'mmr-rakish', 5, 5);
        $this->recordVisits('2026-10-01', 'mmr-simun', 15, 16);
        $this->recordVisits('2026-10-01', 'adris-vadrouille', 20, 25);
        $this->recordVisits('2026-10-01', 'ncm-munich', 15, 30);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-visits?period=30d');

        $response->assertOk()
            ->assertJsonPath('brands', [
                ['marque' => 'mmr', 'visitors' => 30, 'views' => 33],
                ['marque' => 'adris', 'visitors' => 20, 'views' => 25],
                ['marque' => 'ncm', 'visitors' => 15, 'views' => 30],
            ]);

        $this->assertSame(
            ['adris-vadrouille', 'ncm-munich', 'mmr-rakish', 'mmr-simun'],
            array_column($response->json('fiches'), 'slug'),
        );
        $response->assertJsonPath('fiches.2', ['slug' => 'mmr-rakish', 'marque' => 'mmr', 'visitors' => 15, 'views' => 17]);
    }

    #[Test]
    public function it_filters_figures_by_brand_but_keeps_every_brand_in_the_brand_ranking(): void
    {
        $this->recordVisits('2026-10-01', 'mmr-rakish', 10, 12);
        $this->recordVisits('2026-10-01', 'mmr-simun', 5, 5);
        $this->recordVisits('2026-10-01', 'adris-vadrouille', 20, 25);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/website-visits?period=30d&marque=mmr');

        $response->assertOk()
            ->assertJsonPath('kpis.visitors.value', 15)
            ->assertJsonPath('kpis.fiches.value', 2)
            ->assertJsonCount(2, 'brands');

        $this->assertSame(['mmr-rakish', 'mmr-simun'], array_column($response->json('fiches'), 'slug'));
        $this->assertSame(15, array_sum(array_column($response->json('series'), 'visitors')));
    }

    #[Test]
    public function it_defaults_to_the_last_30_days(): void
    {
        $this->recordVisits('2026-01-01', 'mmr-rakish', 1, 1);

        $this->actingAs($this->user)
            ->getJson('/api/dashboard/website-visits')
            ->assertOk()
            ->assertJsonPath('range.start', '2026-09-11')
            ->assertJsonCount(30, 'series');
    }

    #[Test]
    public function it_reports_when_no_visit_has_been_recorded_yet(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/dashboard/website-visits')
            ->assertOk()
            ->assertExactJson(['configured' => true, 'has_data' => false]);
    }

    #[Test]
    public function it_reports_that_tracking_is_not_configured(): void
    {
        config(['database.connections.tracking.database' => null]);

        $this->actingAs($this->user)
            ->getJson('/api/dashboard/website-visits')
            ->assertOk()
            ->assertExactJson(['configured' => false]);
    }

    #[Test]
    public function it_answers_503_when_the_tracking_database_is_unreachable(): void
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
            ->getJson('/api/dashboard/website-visits')
            ->assertStatus(503)
            ->assertJsonPath('message', 'Impossible de joindre la base de suivi des visites.');

        Exceptions::assertReported(QueryException::class);
    }

    #[Test]
    public function it_rejects_an_unknown_period(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/dashboard/website-visits?period=2y')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['period' => 'La période demandée est invalide.']);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->getJson('/api/dashboard/website-visits')->assertUnauthorized();
    }
}
