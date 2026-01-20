<?php

namespace Tests\Feature\Feature\Quotes;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteToInvoiceConversionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_quote_can_be_converted_to_invoice(): void
    {
        $quote = Quote::factory()->asQuote()->create();

        $this->assertNull($quote->invoiced_at);
        $this->assertTrue($quote->isQuote());
        $this->assertFalse($quote->isInvoice());

        $quote->convertToInvoice();
        $quote->refresh();

        $this->assertNotNull($quote->invoiced_at);
        $this->assertTrue($quote->isInvoice());
        $this->assertFalse($quote->isQuote());
    }

    public function test_invoice_cannot_be_converted_again(): void
    {
        $quote = Quote::factory()->asInvoice()->create();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Ce document est déjà une facture.');

        $quote->convertToInvoice();
    }

    public function test_quote_is_editable(): void
    {
        $quote = Quote::factory()->asQuote()->create();

        $this->assertTrue($quote->canEdit());
        $this->assertTrue($quote->canDelete());
    }

    public function test_invoice_is_not_editable(): void
    {
        $quote = Quote::factory()->asInvoice()->create();

        $this->assertFalse($quote->canEdit());
        $this->assertFalse($quote->canDelete());
    }

    public function test_invoice_cannot_be_deleted(): void
    {
        $quote = Quote::factory()->asInvoice()->create();

        $response = $this->delete(route('atelier.quotes.destroy', $quote));

        $response->assertRedirect(route('atelier.quotes.index'));
        $response->assertSessionHas('error', 'Impossible de supprimer une facture.');

        $this->assertDatabaseHas('quotes', ['id' => $quote->id]);
    }

    public function test_quote_can_be_deleted(): void
    {
        $quote = Quote::factory()->asQuote()->create();

        $response = $this->delete(route('atelier.quotes.destroy', $quote));

        $response->assertRedirect(route('atelier.quotes.index'));
        $response->assertSessionHas('message', 'Devis supprimé avec succès.');

        $this->assertSoftDeleted('quotes', ['id' => $quote->id]);
    }

    public function test_quotes_list_shows_correct_type(): void
    {
        $quote = Quote::factory()->asQuote()->create();
        $invoice = Quote::factory()->asInvoice()->create();

        $response = $this->get(route('atelier.quotes.index'));

        $response->assertStatus(200);
        $response->assertSee('Devis');
        $response->assertSee('Facture');
    }

    public function test_invoice_show_page_hides_edit_button(): void
    {
        $invoice = Quote::factory()->asInvoice()->create();

        $response = $this->get(route('atelier.quotes.show', $invoice));

        $response->assertStatus(200);
        $response->assertDontSee('Modifier');
        $response->assertSee('Facture');
    }

    public function test_quote_show_page_shows_edit_button(): void
    {
        $quote = Quote::factory()->asQuote()->create();

        $response = $this->get(route('atelier.quotes.show', $quote));

        $response->assertStatus(200);
        $response->assertSee('Modifier');
        $response->assertSee('Devis');
    }
}
