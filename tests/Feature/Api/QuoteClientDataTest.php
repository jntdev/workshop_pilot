<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteClientDataTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_creating_quote_creates_client_with_all_fields(): void
    {
        $payload = $this->validQuotePayload([
            'client_id' => null,
            'client_prenom' => 'Jean',
            'client_nom' => 'Dupont',
            'client_email' => 'jean@example.com',
            'client_telephone' => '0612345678',
            'client_adresse' => '123 rue Test',
            'client_origine_contact' => 'Recommandation',
            'client_commentaires' => 'Client fidèle',
            'client_avantage_type' => 'pourcentage',
            'client_avantage_valeur' => 10,
            'client_avantage_expiration' => '2025-12-31',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/quotes', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('clients', [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 rue Test',
            'origine_contact' => 'Recommandation',
            'commentaires' => 'Client fidèle',
            'avantage_type' => 'pourcentage',
            'avantage_valeur' => 10,
            'avantage_expiration' => '2025-12-31',
        ]);
    }

    public function test_creating_quote_with_existing_client_updates_all_fields(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'origine_contact' => 'Internet',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ]);

        $payload = $this->validQuotePayload([
            'client_id' => $client->id,
            'client_prenom' => 'Jean',
            'client_nom' => 'Dupont',
            'client_email' => 'jean.new@example.com',
            'client_telephone' => '0612345678',
            'client_adresse' => '456 nouvelle adresse',
            'client_origine_contact' => 'Recommandation',
            'client_commentaires' => 'Mise à jour',
            'client_avantage_type' => 'montant',
            'client_avantage_valeur' => 50,
            'client_avantage_expiration' => '2026-06-30',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/quotes', $payload);

        $response->assertStatus(201);

        $client->refresh();
        $this->assertEquals('jean.new@example.com', $client->email);
        $this->assertEquals('456 nouvelle adresse', $client->adresse);
        $this->assertEquals('Recommandation', $client->origine_contact);
        $this->assertEquals('Mise à jour', $client->commentaires);
        $this->assertEquals('montant', $client->avantage_type);
        $this->assertEquals(50, $client->avantage_valeur);
        $this->assertEquals('2026-06-30', $client->avantage_expiration->format('Y-m-d'));
    }

    public function test_creating_quote_with_default_avantage_values(): void
    {
        $payload = $this->validQuotePayload([
            'client_id' => null,
            'client_prenom' => 'Marie',
            'client_nom' => 'Martin',
            'client_email' => 'marie@example.com',
            'client_telephone' => '0698765432',
            // Champs optionnels non fournis
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/quotes', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('clients', [
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ]);
    }

    public function test_existing_client_data_preserved_when_unchanged(): void
    {
        // Créer un client avec toutes les données métier
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0612345678',
            'adresse' => '123 rue Test',
            'origine_contact' => 'Recommandation fidélité',
            'commentaires' => 'Très bon client depuis 5 ans',
            'avantage_type' => 'pourcentage',
            'avantage_valeur' => 15,
            'avantage_expiration' => '2027-12-31',
        ]);

        // Créer un devis en renvoyant les mêmes données client (simulant un formulaire bien rempli)
        $payload = $this->validQuotePayload([
            'client_id' => $client->id,
            'client_prenom' => 'Jean',
            'client_nom' => 'Dupont',
            'client_email' => 'jean@example.com',
            'client_telephone' => '0612345678',
            'client_adresse' => '123 rue Test',
            'client_origine_contact' => 'Recommandation fidélité',
            'client_commentaires' => 'Très bon client depuis 5 ans',
            'client_avantage_type' => 'pourcentage',
            'client_avantage_valeur' => 15,
            'client_avantage_expiration' => '2027-12-31',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/api/quotes', $payload);

        $response->assertStatus(201);

        // Vérifier que les données client sont intactes
        $client->refresh();
        $this->assertEquals('Recommandation fidélité', $client->origine_contact);
        $this->assertEquals('Très bon client depuis 5 ans', $client->commentaires);
        $this->assertEquals('pourcentage', $client->avantage_type);
        $this->assertEquals(15, $client->avantage_valeur);
        $this->assertEquals('2027-12-31', $client->avantage_expiration->format('Y-m-d'));
    }

    public function test_api_clients_returns_all_client_fields(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'email' => 'marie@example.com',
            'telephone' => '0698765432',
            'origine_contact' => 'Publicité locale',
            'commentaires' => 'Cliente régulière',
            'avantage_type' => 'montant',
            'avantage_valeur' => 25,
            'avantage_expiration' => '2026-06-15',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/clients?search=Marie');

        $response->assertStatus(200);

        $responseData = $response->json();
        $foundClient = collect($responseData)->firstWhere('id', $client->id);

        $this->assertNotNull($foundClient);
        $this->assertEquals('Marie', $foundClient['prenom']);
        $this->assertEquals('Martin', $foundClient['nom']);
        $this->assertEquals('Publicité locale', $foundClient['origine_contact']);
        $this->assertEquals('Cliente régulière', $foundClient['commentaires']);
        $this->assertEquals('montant', $foundClient['avantage_type']);
        $this->assertEquals(25, (float) $foundClient['avantage_valeur']);
        $this->assertEquals('2026-06-15', $foundClient['avantage_expiration']);
    }

    public function test_quote_validation_returns_422_with_errors(): void
    {
        $payload = [
            'client_prenom' => '',
            'client_nom' => '',
            'bike_description' => '',
            'reception_comment' => '',
            'valid_until' => '',
            'lines' => [],
            'totals' => [],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/quotes', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'client_prenom',
                'client_nom',
                'bike_description',
                'reception_comment',
                'valid_until',
                'lines',
            ]);
    }

    public function test_inertia_clients_index_returns_all_client_fields(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Sophie',
            'nom' => 'Bernard',
            'email' => 'sophie@example.com',
            'telephone' => '0611223344',
            'adresse' => '789 avenue Test',
            'origine_contact' => 'Site web',
            'commentaires' => 'Nouvelle cliente',
            'avantage_type' => 'pourcentage',
            'avantage_valeur' => 5,
            'avantage_expiration' => '2026-12-31',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/clients');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Clients/Index')
            ->has('clients', 1)
            ->where('clients.0.id', $client->id)
            ->where('clients.0.prenom', 'Sophie')
            ->where('clients.0.nom', 'Bernard')
            ->where('clients.0.origine_contact', 'Site web')
            ->where('clients.0.commentaires', 'Nouvelle cliente')
            ->where('clients.0.avantage_type', 'pourcentage')
            ->where('clients.0.avantage_valeur', fn ($value) => (float) $value === 5.0)
            ->where('clients.0.avantage_expiration', '2026-12-31')
        );
    }

    private function validQuotePayload(array $overrides = []): array
    {
        $defaults = [
            'client_id' => null,
            'client_prenom' => 'Test',
            'client_nom' => 'Client',
            'client_email' => 'test@example.com',
            'client_telephone' => '0600000000',
            'client_adresse' => null,
            'client_origine_contact' => null,
            'client_commentaires' => null,
            'client_avantage_type' => 'aucun',
            'client_avantage_valeur' => 0,
            'client_avantage_expiration' => null,
            'bike_description' => 'VTT bleu',
            'reception_comment' => 'Révision complète',
            'valid_until' => now()->addDays(15)->format('Y-m-d'),
            'discount_type' => null,
            'discount_value' => null,
            'lines' => [
                [
                    'title' => 'Réparation',
                    'reference' => null,
                    'quantity' => 1,
                    'purchase_price_ht' => 50,
                    'sale_price_ht' => 100,
                    'sale_price_ttc' => 120,
                    'margin_amount_ht' => 50,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_purchase_ht' => 50,
                    'line_margin_ht' => 50,
                    'line_total_ht' => 100,
                    'line_total_ttc' => 120,
                ],
            ],
            'totals' => [
                'total_ht' => 100,
                'total_tva' => 20,
                'total_ttc' => 120,
                'margin_total_ht' => 50,
            ],
        ];

        return array_merge($defaults, $overrides);
    }
}
