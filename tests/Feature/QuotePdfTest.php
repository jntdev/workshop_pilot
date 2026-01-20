<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuotePdfTest extends TestCase
{
    protected function getTestUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'test@workshop-pilot.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );
    }

    #[Test]
    public function it_generates_pdf_for_quote(): void
    {
        $user = $this->getTestUser();
        $client = Client::firstOrCreate(
            ['email' => 'jean.test@example.com'],
            [
                'prenom' => 'Jean',
                'nom' => 'Dupont',
                'telephone' => '0123456789',
                'adresse' => '123 rue Test',
            ]
        );

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'reference' => 'DEV-TEST-' . time(),
            'total_ht' => '100.00',
            'total_tva' => '20.00',
            'total_ttc' => '120.00',
            'margin_total_ht' => '30.00',
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'title' => 'Prestation test',
            'reference' => 'REF-001',
            'quantity' => '1.00',
            'purchase_price_ht' => '70.00',
            'sale_price_ht' => '100.00',
            'sale_price_ttc' => '120.00',
            'margin_amount_ht' => '30.00',
            'margin_rate' => '30.0000',
            'tva_rate' => '20.0000',
        ]);

        $response = $this->actingAs($user)->get(route('atelier.quotes.pdf', $quote));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertDownload('devis-' . $quote->reference . '.pdf');
    }

    #[Test]
    public function it_generates_pdf_for_invoice(): void
    {
        $user = $this->getTestUser();
        $client = Client::firstOrCreate(
            ['email' => 'marie.test@example.com'],
            [
                'prenom' => 'Marie',
                'nom' => 'Martin',
                'telephone' => '0198765432',
            ]
        );

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'reference' => 'FACT-TEST-' . time(),
            'invoiced_at' => now(),
            'total_ht' => '200.00',
            'total_tva' => '40.00',
            'total_ttc' => '240.00',
            'margin_total_ht' => '50.00',
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'title' => 'Prestation facturée',
            'reference' => 'REF-002',
            'quantity' => '1.00',
            'purchase_price_ht' => '150.00',
            'sale_price_ht' => '200.00',
            'sale_price_ttc' => '240.00',
            'margin_amount_ht' => '50.00',
            'margin_rate' => '25.0000',
            'tva_rate' => '20.0000',
        ]);

        $response = $this->actingAs($user)->get(route('atelier.quotes.pdf', $quote));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertDownload('facture-' . $quote->reference . '.pdf');
    }

    #[Test]
    public function it_generates_pdf_with_multiple_lines(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'reference' => 'DEV-MULTI-' . time(),
            'total_ht' => '300.00',
            'total_tva' => '60.00',
            'total_ttc' => '360.00',
            'margin_total_ht' => '90.00',
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'title' => 'Prestation 1',
            'reference' => 'REF-001',
            'quantity' => '1.00',
            'purchase_price_ht' => '100.00',
            'sale_price_ht' => '150.00',
            'sale_price_ttc' => '180.00',
            'margin_amount_ht' => '50.00',
            'margin_rate' => '33.3333',
            'tva_rate' => '20.0000',
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'title' => 'Prestation 2',
            'reference' => 'REF-002',
            'quantity' => '1.00',
            'purchase_price_ht' => '110.00',
            'sale_price_ht' => '150.00',
            'sale_price_ttc' => '180.00',
            'margin_amount_ht' => '40.00',
            'margin_rate' => '26.6667',
            'tva_rate' => '20.0000',
        ]);

        $response = $this->actingAs($user)->get(route('atelier.quotes.pdf', $quote));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function it_requires_authentication_to_download_pdf(): void
    {
        $quote = Quote::factory()->create();

        $response = $this->get(route('atelier.quotes.pdf', $quote));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_uses_consistent_filename_for_quote(): void
    {
        $user = $this->getTestUser();
        $reference = 'DEV-CONST-' . time();
        $quote = Quote::factory()->create([
            'reference' => $reference,
        ]);

        QuoteLine::factory()->create(['quote_id' => $quote->id]);

        $response1 = $this->actingAs($user)->get(route('atelier.quotes.pdf', $quote));
        $response1->assertDownload('devis-' . $reference . '.pdf');

        $response2 = $this->actingAs($user)->get(route('atelier.quotes.pdf', $quote));
        $response2->assertDownload('devis-' . $reference . '.pdf');
    }
}
