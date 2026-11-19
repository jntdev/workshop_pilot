<?php

namespace Tests\Feature\Api;

use App\Enums\QuoteStatus;
use App\Models\AgendaEvent;
use App\Models\Article;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteAppointment;
use App\Models\QuoteComment;
use App\Models\QuoteLine;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssistantReadTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'secret-read-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['assistant.read_token' => self::TOKEN]);
    }

    /**
     * @return array<string, string>
     */
    private function auth(): array
    {
        return ['Authorization' => 'Bearer '.self::TOKEN];
    }

    #[Test]
    public function it_requires_a_search_term_to_look_for_clients(): void
    {
        Client::factory()->create();

        $this->getJson('/api/assistant-read/clients', $this->auth())->assertUnprocessable();
        $this->getJson('/api/assistant-read/clients?search=a', $this->auth())->assertUnprocessable();
    }

    #[Test]
    public function it_finds_clients_by_name_phone_or_email(): void
    {
        Client::factory()->create(['prenom' => 'Yann', 'nom' => 'Le Gall', 'telephone' => '0601020304', 'email' => 'yann@example.test']);
        Client::factory()->create(['prenom' => 'Marie', 'nom' => 'Kerbrat', 'telephone' => '0611223344', 'email' => 'marie@example.test']);

        $this->getJson('/api/assistant-read/clients?search=gall', $this->auth())
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.prenom', 'Yann');

        $this->getJson('/api/assistant-read/clients?search=0611', $this->auth())
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.nom', 'Kerbrat');

        $this->getJson('/api/assistant-read/clients?search=marie%20kerbrat', $this->auth())
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.prenom', 'Marie');
    }

    #[Test]
    public function a_wildcard_in_the_search_does_not_match_everything(): void
    {
        Client::factory()->count(3)->create();

        $this->getJson('/api/assistant-read/clients?search=%25%25', $this->auth())
            ->assertOk()->assertJsonCount(0);
    }

    #[Test]
    public function it_returns_a_client_with_their_quotes(): void
    {
        $client = Client::factory()->create(['commentaires' => 'Client fidèle']);
        Quote::factory()->count(2)->create(['client_id' => $client->id]);
        Quote::factory()->create();

        $this->getJson("/api/assistant-read/clients/{$client->id}", $this->auth())
            ->assertOk()
            ->assertJsonPath('id', $client->id)
            ->assertJsonPath('commentaires', 'Client fidèle')
            ->assertJsonCount(2, 'quotes');
    }

    #[Test]
    public function it_answers_404_for_an_unknown_client_or_quote(): void
    {
        $this->getJson('/api/assistant-read/clients/999999', $this->auth())->assertNotFound();
        $this->getJson('/api/assistant-read/quotes/999999', $this->auth())->assertNotFound();
    }

    #[Test]
    public function it_filters_quotes_by_status_and_search(): void
    {
        $client = Client::factory()->create(['nom' => 'Kerbrat']);
        Quote::factory()->create(['client_id' => $client->id, 'status' => QuoteStatus::PendingValidation, 'bike_description' => 'VTT rouge']);
        Quote::factory()->create(['client_id' => $client->id, 'status' => QuoteStatus::Done, 'bike_description' => 'Vélo de ville']);
        Quote::factory()->create(['status' => QuoteStatus::PendingValidation, 'bike_description' => 'Gravel']);

        $this->getJson('/api/assistant-read/quotes?status=pending_validation', $this->auth())
            ->assertOk()->assertJsonCount(2);

        $this->getJson('/api/assistant-read/quotes?search=kerbrat', $this->auth())
            ->assertOk()->assertJsonCount(2);

        $this->getJson('/api/assistant-read/quotes?search=kerbrat&status=done', $this->auth())
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.bike_description', 'Vélo de ville');

        $this->getJson('/api/assistant-read/quotes?status=inconnu', $this->auth())->assertUnprocessable();
    }

    #[Test]
    public function it_excludes_archived_quotes_from_the_list(): void
    {
        Quote::factory()->create(['is_archived' => true]);
        Quote::factory()->create(['is_archived' => false]);

        $this->getJson('/api/assistant-read/quotes', $this->auth())->assertOk()->assertJsonCount(1);
    }

    #[Test]
    public function a_quote_exposes_lines_comments_and_appointments_but_no_purchase_price_or_margin(): void
    {
        $quote = Quote::factory()->create([
            'status' => QuoteStatus::Validated,
            'total_ttc' => '120.00',
            'margin_total_ht' => '35.00',
        ]);
        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'title' => 'Gaine et câble',
            'purchase_price_ht' => '4.50',
            'margin_amount_ht' => '7.77',
        ]);
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'Appeler le client']);
        QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'starts_at' => '2026-11-20 08:00:00',
            'ends_at' => '2026-11-20 09:00:00',
        ]);

        $response = $this->getJson("/api/assistant-read/quotes/{$quote->id}", $this->auth())
            ->assertOk()
            ->assertJsonPath('status_label', 'Validé')
            ->assertJsonPath('total_ttc', '120.00')
            ->assertJsonPath('lines.0.title', 'Gaine et câble')
            ->assertJsonPath('comments.0.content', 'Appeler le client')
            ->assertJsonPath('appointments.0.starts_at', '20/11/2026 09:00')
            ->assertJsonPath('appointments.0.ends_at', '20/11/2026 10:00');

        $raw = $response->getContent();
        foreach (['purchase_price', 'margin', 'line_purchase', '4.50', '7.77', '35.00'] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $raw);
        }
    }

    #[Test]
    public function it_lists_agenda_items_in_paris_time_and_sorted(): void
    {
        $quote = Quote::factory()->create();
        QuoteAppointment::factory()->create(['quote_id' => $quote->id, 'starts_at' => '2026-11-20 13:00:00', 'ends_at' => '2026-11-20 14:00:00']);
        AgendaEvent::factory()->create(['title' => 'Livraison fournisseur', 'starts_at' => '2026-11-20 08:00:00', 'ends_at' => '2026-11-20 08:30:00']);
        AgendaEvent::factory()->create(['title' => 'Hors période', 'starts_at' => '2026-12-20 08:00:00', 'ends_at' => '2026-12-20 08:30:00']);

        $this->getJson('/api/assistant-read/agenda?start=2026-11-20&end=2026-11-20', $this->auth())
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.kind', 'evenement')
            ->assertJsonPath('0.title', 'Livraison fournisseur')
            ->assertJsonPath('0.starts_at', '20/11/2026 09:00')
            ->assertJsonPath('1.kind', 'devis')
            ->assertJsonPath('1.starts_at', '20/11/2026 14:00');
    }

    #[Test]
    public function the_agenda_refuses_a_period_longer_than_two_months(): void
    {
        $this->getJson('/api/assistant-read/agenda?start=2026-01-01&end=2026-06-01', $this->auth())->assertUnprocessable();
        $this->getJson('/api/assistant-read/agenda?start=2026-11-20&end=2026-11-10', $this->auth())->assertUnprocessable();
    }

    #[Test]
    public function it_searches_articles_with_price_in_euros_and_stock_but_no_purchase_price(): void
    {
        $article = Article::factory()->create([
            'reference' => 'KMC-X9-U',
            'designation' => 'Chaîne KMC 9 vitesses',
            'sale_price_ttc' => 2400,
            'purchase_price_ht' => 1111,
        ]);
        StockMovement::factory()->create(['article_id' => $article->id, 'quantity' => 5]);
        Article::factory()->create(['reference' => 'AUTRE-1', 'designation' => 'Selle']);

        $response = $this->getJson('/api/assistant-read/articles?search=kmc', $this->auth())
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.reference', 'KMC-X9-U')
            ->assertJsonPath('0.sale_price_ttc_eur', '24.00')
            ->assertJsonPath('0.stock_quantity', 5);

        $this->assertStringNotContainsString('purchase_price', $response->getContent());
        $this->assertStringNotContainsString('1111', $response->getContent());
    }

    #[Test]
    public function it_requires_a_search_term_to_look_for_articles(): void
    {
        $this->getJson('/api/assistant-read/articles', $this->auth())->assertUnprocessable();
    }
}
