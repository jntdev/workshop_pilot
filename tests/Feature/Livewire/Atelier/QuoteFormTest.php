<?php

namespace Tests\Feature\Livewire\Atelier;

use App\Livewire\Atelier\Quotes\Form;
use App\Models\Client;
use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_does_not_update_client_when_data_unchanged(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0123456789',
            'adresse' => '123 rue Test',
        ]);

        $quote = Quote::factory()->create(['client_id' => $client->id]);

        // Sauvegarder sans modifier les données client
        $originalUpdatedAt = $client->fresh()->updated_at;

        $this->travel(5)->seconds();

        // Charger le formulaire en mode édition et sauvegarder sans modification client
        Livewire::test(Form::class, ['quoteId' => $quote->id])
            ->call('save', true);

        // Vérifier que le client n'a PAS été mis à jour
        $client->refresh();
        $this->assertEquals($originalUpdatedAt->timestamp, $client->updated_at->timestamp);
        $this->assertEquals('Jean', $client->prenom);
        $this->assertEquals('Dupont', $client->nom);
    }

    #[Test]
    public function it_updates_client_when_data_is_changed(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean@example.com',
            'telephone' => '0123456789',
            'adresse' => '123 rue Test',
        ]);

        $quote = Quote::factory()->create(['client_id' => $client->id]);

        $originalUpdatedAt = $client->fresh()->updated_at;

        $this->travel(5)->seconds();

        // Charger le formulaire et MODIFIER les données client
        Livewire::test(Form::class, ['quoteId' => $quote->id])
            ->set('clientNom', 'Martin')
            ->call('save', true);

        // Vérifier que le client A été mis à jour
        $client->refresh();
        $this->assertNotEquals($originalUpdatedAt->timestamp, $client->updated_at->timestamp);
        $this->assertEquals('Jean', $client->prenom);
        $this->assertEquals('Martin', $client->nom);
    }

    #[Test]
    public function it_updates_client_when_email_is_changed(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean@example.com',
        ]);

        $quote = Quote::factory()->create(['client_id' => $client->id]);

        // Modifier uniquement l'email
        Livewire::test(Form::class, ['quoteId' => $quote->id])
            ->set('clientEmail', 'jean.dupont@example.com')
            ->call('save', true);

        // Vérifier que l'email a été mis à jour
        $client->refresh();
        $this->assertEquals('jean.dupont@example.com', $client->email);
    }

    #[Test]
    public function it_creates_new_client_when_no_client_selected(): void
    {
        $initialClientCount = Client::count();

        Livewire::test(Form::class)
            ->set('clientPrenom', 'Marie')
            ->set('clientNom', 'Dubois')
            ->set('clientEmail', 'marie@example.com')
            ->set('clientTelephone', '0987654321')
            ->set('lines.0.title', 'Réparation')
            ->set('lines.0.purchase_price_ht', '50.00')
            ->set('lines.0.sale_price_ht', '100.00')
            ->call('save', true);

        // Vérifier qu'un nouveau client a été créé
        $this->assertEquals($initialClientCount + 1, Client::count());

        $newClient = Client::latest()->first();
        $this->assertEquals('Marie', $newClient->prenom);
        $this->assertEquals('Dubois', $newClient->nom);
        $this->assertEquals('marie@example.com', $newClient->email);
    }

    #[Test]
    public function it_keeps_quote_reference_when_updating(): void
    {
        $client = Client::factory()->create();
        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'reference' => 'DEV-202511-0001',
        ]);

        // Modifier le devis
        Livewire::test(Form::class, ['quoteId' => $quote->id])
            ->set('validUntil', now()->addDays(30)->format('Y-m-d'))
            ->call('save', true);

        // Vérifier que la référence n'a PAS changé
        $quote->refresh();
        $this->assertEquals('DEV-202511-0001', $quote->reference);
    }
}
