<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteTimeTrackingTest extends TestCase
{
    protected function getTestUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'test-time@workshop-pilot.com'],
            [
                'name' => 'Test Time User',
                'password' => bcrypt('password'),
            ]
        );
    }

    protected function makeValidQuotePayload(Client $client, array $overrides = []): array
    {
        return array_merge([
            'client_id' => $client->id,
            'client_prenom' => $client->prenom,
            'client_nom' => $client->nom,
            'client_email' => $client->email,
            'client_telephone' => $client->telephone,
            'client_adresse' => $client->adresse,
            'valid_until' => now()->addDays(30)->toDateString(),
            'bike_description' => 'VTT test',
            'reception_comment' => 'Test time tracking',
            'discount_type' => null,
            'discount_value' => null,
            'totals' => [
                'total_ht' => 85.00,
                'total_tva' => 17.00,
                'total_ttc' => 102.00,
                'margin_total_ht' => 45.00,
            ],
            'lines' => [
                [
                    'title' => 'Révision complète',
                    'reference' => 'REV-001',
                    'quantity' => 1,
                    'purchase_price_ht' => 30,
                    'sale_price_ht' => 60,
                    'sale_price_ttc' => 72,
                    'margin_amount_ht' => 30,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_total_ht' => 60,
                    'line_total_ttc' => 72,
                    'line_margin_ht' => 30,
                    'line_purchase_ht' => 30,
                    'estimated_time_minutes' => 90,
                ],
                [
                    'title' => 'Réglage freins',
                    'reference' => 'FREIN-001',
                    'quantity' => 1,
                    'purchase_price_ht' => 10,
                    'sale_price_ht' => 25,
                    'sale_price_ttc' => 30,
                    'margin_amount_ht' => 15,
                    'margin_rate' => 60,
                    'tva_rate' => 20,
                    'line_total_ht' => 25,
                    'line_total_ttc' => 30,
                    'line_margin_ht' => 15,
                    'line_purchase_ht' => 10,
                    'estimated_time_minutes' => 30,
                ],
            ],
        ], $overrides);
    }

    #[Test]
    public function it_can_create_quote_with_estimated_time_on_lines(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidQuotePayload($client);

        $response = $this->actingAs($user)->postJson('/api/quotes', $payload);

        $response->assertStatus(201);

        $quote = Quote::find($response->json('id'));
        $this->assertNotNull($quote);

        // Vérifie que le temps total estimé est calculé (90 + 30 = 120 minutes)
        $this->assertEquals(120, $quote->total_estimated_time_minutes);

        // Vérifie que les lignes ont bien leur temps estimé
        $lines = $quote->lines()->orderBy('position')->get();
        $this->assertEquals(90, $lines[0]->estimated_time_minutes);
        $this->assertEquals(30, $lines[1]->estimated_time_minutes);
    }

    #[Test]
    public function it_can_update_quote_with_estimated_time_and_actual_time(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'total_estimated_time_minutes' => 60,
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'estimated_time_minutes' => 60,
        ]);

        $payload = $this->makeValidQuotePayload($client, [
            'actual_time_minutes' => 75,
            'lines' => [
                [
                    'title' => 'Révision complète mise à jour',
                    'reference' => 'REV-002',
                    'quantity' => 1,
                    'purchase_price_ht' => 30,
                    'sale_price_ht' => 60,
                    'sale_price_ttc' => 72,
                    'margin_amount_ht' => 30,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_total_ht' => 60,
                    'line_total_ttc' => 72,
                    'line_margin_ht' => 30,
                    'line_purchase_ht' => 30,
                    'estimated_time_minutes' => 120,
                ],
            ],
            'totals' => [
                'total_ht' => 60.00,
                'total_tva' => 12.00,
                'total_ttc' => 72.00,
                'margin_total_ht' => 30.00,
            ],
        ]);

        $response = $this->actingAs($user)->putJson("/api/quotes/{$quote->id}", $payload);

        $response->assertStatus(200);

        $quote->refresh();
        $this->assertEquals(120, $quote->total_estimated_time_minutes);
        $this->assertEquals(75, $quote->actual_time_minutes);
    }

    #[Test]
    public function it_can_update_actual_time_on_invoice(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        // Créer une facture (invoiced_at non null)
        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'invoiced_at' => now(),
            'total_estimated_time_minutes' => 90,
            'actual_time_minutes' => null,
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'estimated_time_minutes' => 90,
        ]);

        // Mise à jour du temps réel via l'endpoint dédié
        $response = $this->actingAs($user)->patchJson("/api/quotes/{$quote->id}/actual-time", [
            'actual_time_minutes' => 105,
        ]);

        $response->assertStatus(200);

        $quote->refresh();
        $this->assertEquals(105, $quote->actual_time_minutes);
    }

    #[Test]
    public function it_returns_time_fields_in_api_response(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'total_estimated_time_minutes' => 180,
            'actual_time_minutes' => 200,
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'estimated_time_minutes' => 180,
        ]);

        $response = $this->actingAs($user)->getJson("/api/quotes/{$quote->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('total_estimated_time_minutes', 180);
        $response->assertJsonPath('actual_time_minutes', 200);
        $response->assertJsonPath('lines.0.estimated_time_minutes', 180);
    }

    #[Test]
    public function it_validates_time_fields_are_positive_integers(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidQuotePayload($client, [
            'actual_time_minutes' => -10,
            'lines' => [
                [
                    'title' => 'Test',
                    'reference' => 'TEST-001',
                    'quantity' => 1,
                    'purchase_price_ht' => 30,
                    'sale_price_ht' => 60,
                    'sale_price_ttc' => 72,
                    'margin_amount_ht' => 30,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_total_ht' => 60,
                    'line_total_ttc' => 72,
                    'line_margin_ht' => 30,
                    'line_purchase_ht' => 30,
                    'estimated_time_minutes' => -5,
                ],
            ],
        ]);

        $response = $this->actingAs($user)->postJson('/api/quotes', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['actual_time_minutes', 'lines.0.estimated_time_minutes']);
    }

    #[Test]
    public function it_allows_null_time_values(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidQuotePayload($client, [
            'actual_time_minutes' => null,
            'lines' => [
                [
                    'title' => 'Sans temps estimé',
                    'reference' => 'TEST-002',
                    'quantity' => 1,
                    'purchase_price_ht' => 30,
                    'sale_price_ht' => 60,
                    'sale_price_ttc' => 72,
                    'margin_amount_ht' => 30,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_total_ht' => 60,
                    'line_total_ttc' => 72,
                    'line_margin_ht' => 30,
                    'line_purchase_ht' => 30,
                    'estimated_time_minutes' => null,
                ],
            ],
            'totals' => [
                'total_ht' => 60.00,
                'total_tva' => 12.00,
                'total_ttc' => 72.00,
                'margin_total_ht' => 30.00,
            ],
        ]);

        $response = $this->actingAs($user)->postJson('/api/quotes', $payload);

        $response->assertStatus(201);

        $quote = Quote::find($response->json('id'));
        $this->assertNull($quote->actual_time_minutes);
        $this->assertNull($quote->total_estimated_time_minutes);
    }

    #[Test]
    public function it_calculates_total_estimated_time_from_lines(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidQuotePayload($client, [
            'lines' => [
                [
                    'title' => 'Ligne 1',
                    'reference' => 'L1',
                    'quantity' => 1,
                    'purchase_price_ht' => 10,
                    'sale_price_ht' => 20,
                    'sale_price_ttc' => 24,
                    'margin_amount_ht' => 10,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_total_ht' => 20,
                    'line_total_ttc' => 24,
                    'line_margin_ht' => 10,
                    'line_purchase_ht' => 10,
                    'estimated_time_minutes' => 30,
                ],
                [
                    'title' => 'Ligne 2',
                    'reference' => 'L2',
                    'quantity' => 1,
                    'purchase_price_ht' => 10,
                    'sale_price_ht' => 20,
                    'sale_price_ttc' => 24,
                    'margin_amount_ht' => 10,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_total_ht' => 20,
                    'line_total_ttc' => 24,
                    'line_margin_ht' => 10,
                    'line_purchase_ht' => 10,
                    'estimated_time_minutes' => 45,
                ],
                [
                    'title' => 'Ligne 3 sans temps',
                    'reference' => 'L3',
                    'quantity' => 1,
                    'purchase_price_ht' => 10,
                    'sale_price_ht' => 20,
                    'sale_price_ttc' => 24,
                    'margin_amount_ht' => 10,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_total_ht' => 20,
                    'line_total_ttc' => 24,
                    'line_margin_ht' => 10,
                    'line_purchase_ht' => 10,
                    'estimated_time_minutes' => null,
                ],
                [
                    'title' => 'Ligne 4',
                    'reference' => 'L4',
                    'quantity' => 1,
                    'purchase_price_ht' => 10,
                    'sale_price_ht' => 20,
                    'sale_price_ttc' => 24,
                    'margin_amount_ht' => 10,
                    'margin_rate' => 50,
                    'tva_rate' => 20,
                    'line_total_ht' => 20,
                    'line_total_ttc' => 24,
                    'line_margin_ht' => 10,
                    'line_purchase_ht' => 10,
                    'estimated_time_minutes' => 60,
                ],
            ],
            'totals' => [
                'total_ht' => 80.00,
                'total_tva' => 16.00,
                'total_ttc' => 96.00,
                'margin_total_ht' => 40.00,
            ],
        ]);

        $response = $this->actingAs($user)->postJson('/api/quotes', $payload);

        $response->assertStatus(201);

        $quote = Quote::find($response->json('id'));
        // Total = 30 + 45 + 0 + 60 = 135 minutes
        $this->assertEquals(135, $quote->total_estimated_time_minutes);
    }

    #[Test]
    public function it_exposes_time_fields_in_inertia_show_response(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'total_estimated_time_minutes' => 120,
            'actual_time_minutes' => 90,
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'estimated_time_minutes' => 120,
        ]);

        $response = $this->actingAs($user)->get(route('atelier.quotes.show', $quote));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Atelier/Quotes/Show')
            ->has('quote', fn ($quote) => $quote
                ->where('total_estimated_time_minutes', 120)
                ->where('actual_time_minutes', 90)
                ->has('lines.0', fn ($line) => $line
                    ->where('estimated_time_minutes', 120)
                    ->etc()
                )
                ->etc()
            )
        );
    }

    #[Test]
    public function it_exposes_time_fields_in_inertia_edit_response(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'total_estimated_time_minutes' => 60,
            'actual_time_minutes' => 45,
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'estimated_time_minutes' => 60,
        ]);

        $response = $this->actingAs($user)->get(route('atelier.quotes.edit', $quote));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Atelier/Quotes/Form')
            ->has('quote', fn ($quote) => $quote
                ->where('total_estimated_time_minutes', 60)
                ->where('actual_time_minutes', 45)
                ->has('lines.0', fn ($line) => $line
                    ->where('estimated_time_minutes', 60)
                    ->etc()
                )
                ->etc()
            )
        );
    }
}
