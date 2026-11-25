<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteMailLogTest extends TestCase
{
    use RefreshDatabase;

    private string $logPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logPath = sys_get_temp_dir().'/laravel-mails-'.uniqid().'.log';
        config([
            'logging.channels.mails.path' => $this->logPath,
            'mail.default' => 'array',
        ]);
    }

    protected function tearDown(): void
    {
        @unlink($this->logPath);
        parent::tearDown();
    }

    private function log(): string
    {
        return is_file($this->logPath) ? (string) file_get_contents($this->logPath) : '';
    }

    #[Test]
    public function it_logs_a_quote_sent_by_email_with_its_content(): void
    {
        $quote = Quote::factory()->create(['reference' => '20260923-5', 'total_ttc' => 52.33, 'email_note' => 'Pièce posée.']);
        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'title' => 'Bosch Intuvia Control Unit',
            'quantity' => 1,
            'line_total_ttc' => 52.33,
        ]);

        $this->actingAs(User::factory()->create())
            ->postJson("/api/quotes/{$quote->id}/send-email", ['email' => 'cliente@example.com'])
            ->assertOk();

        $log = $this->log();
        $this->assertStringContainsString('.INFO: Mail envoyé : Devis 20260923-5', $log);
        $this->assertStringContainsString('"to":["cliente@example.com"]', $log);
        $this->assertStringContainsString('"attachments":["devis-20260923-5.pdf"]', $log);
        $this->assertStringContainsString('"type":"devis"', $log);
        $this->assertStringContainsString('"lines_count":1', $log);
        $this->assertStringContainsString('"lines":["Bosch Intuvia Control Unit × 1 = 52.33 € TTC"]', $log);
        $this->assertStringContainsString('"email_note":"Pi', $log);
        $this->assertStringNotContainsString('logo-email', $log);
    }

    #[Test]
    public function it_logs_an_invoice_as_such(): void
    {
        $quote = Quote::factory()->create(['reference' => '20261125-1', 'invoiced_at' => now()]);

        $this->actingAs(User::factory()->create())
            ->postJson("/api/quotes/{$quote->id}/send-email", ['email' => 'client@example.com'])
            ->assertOk();

        $this->assertStringContainsString('Mail envoyé : Facture 20261125-1', $this->log());
        $this->assertStringContainsString('"attachments":["facture-20261125-1.pdf"]', $this->log());
        $this->assertStringContainsString('"type":"facture"', $this->log());
    }

    #[Test]
    public function it_logs_a_failed_sending(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1,
            'mail.mailers.smtp.timeout' => 2,
        ]);
        $quote = Quote::factory()->create(['reference' => '20261125-2']);

        $this->actingAs(User::factory()->create())
            ->postJson("/api/quotes/{$quote->id}/send-email", ['email' => 'client@example.com'])
            ->assertStatus(500)
            ->assertJsonPath('success', false);

        $log = $this->log();
        $this->assertStringContainsString('.ERROR: Échec de l\'envoi du mail : Devis 20261125-2', $log);
        $this->assertStringContainsString('"quote_id":'.$quote->id, $log);
        $this->assertStringNotContainsString('Mail envoyé', $log);
    }

    #[Test]
    public function it_logs_other_mails_without_quote_details(): void
    {
        Mail::raw('Bonjour', fn ($message) => $message->to('autre@example.com')->subject('Essai'));

        $log = $this->log();
        $this->assertStringContainsString('Mail envoyé : Essai', $log);
        $this->assertStringContainsString('"to":["autre@example.com"]', $log);
        $this->assertStringNotContainsString('"quote"', $log);
    }
}
