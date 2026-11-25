<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActionLogTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'secret-ai-token';

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir().'/actions-'.uniqid();
        mkdir($this->dir);
        config([
            'logging.channels.actions.path' => $this->dir.'/laravel-actions.log',
            'ai.token' => self::TOKEN,
        ]);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir.'/*') ?: []);
        @rmdir($this->dir);
        parent::tearDown();
    }

    /** Contenu du journal des actions (un fichier par jour). */
    private function log(): string
    {
        return implode('', array_map('file_get_contents', glob($this->dir.'/laravel-actions*.log') ?: []));
    }

    #[Test]
    public function it_logs_a_successful_action_with_its_author_and_data(): void
    {
        $user = User::factory()->create(['name' => 'Nicolas']);

        $response = $this->actingAs($user)->postJson('/api/clients', [
            'prenom' => 'Béatrice',
            'nom' => 'Ceveno',
            'telephone' => '0600000000',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
            'password' => 'ne-doit-pas-apparaitre',
        ])->assertCreated();

        $log = $this->log();
        $this->assertStringContainsString('.INFO: Action : POST /api/clients → 201', $log);
        $this->assertStringContainsString('"actor":"user#'.$user->id.' Nicolas"', $log);
        $this->assertStringContainsString('"action":"Api\\\\ClientController@store"', $log);
        $this->assertStringContainsString('"result_id":'.$response->json('data.id'), $log);
        $this->assertStringContainsString('"prenom":"Béatrice"', $log);
        $this->assertStringContainsString('"password":"[masqué]"', $log);
        $this->assertStringNotContainsString('ne-doit-pas-apparaitre', $log);
    }

    #[Test]
    public function it_logs_failed_actions_too(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/api/clients', ['prenom' => 'Sans nom'])
            ->assertUnprocessable();

        $this->assertStringContainsString('.WARNING: Action : POST /api/clients → 422', $this->log());
    }

    #[Test]
    public function it_logs_ai_api_actions_with_the_record_concerned(): void
    {
        $quote = Quote::factory()->create();

        $this->withHeader('Authorization', 'Bearer '.self::TOKEN)
            ->patchJson("/api/ai/quotes/{$quote->id}/status", ['status' => 'done']);

        $log = $this->log();
        $this->assertStringContainsString('Action : PATCH /api/ai/quotes/'.$quote->id.'/status', $log);
        $this->assertStringContainsString('"actor":"api-ai"', $log);
        $this->assertStringContainsString('"records":{"quote":"Quote#'.$quote->id.'"}', $log);
    }

    #[Test]
    public function it_does_not_log_reads_nor_price_calculations(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($user)->getJson("/api/clients/{$client->id}")->assertOk();
        $this->actingAs($user)->postJson('/api/quotes/calculate-line', [
            'purchase_price_ht' => 10,
            'tva_rate' => 20,
            'calculation_type' => 'sale_price_ttc',
            'value' => 24,
        ])->assertOk();

        $this->assertSame('', $this->log());
    }

    #[Test]
    public function it_masks_uploaded_files_and_truncates_long_values(): void
    {
        Route::post('/test-journal', fn () => response()->json(['ok' => true]))->middleware('web');

        $this->post('/test-journal', [
            'note' => str_repeat('a', 1000),
            'piece' => UploadedFile::fake()->create('facture.pdf', 12),
            'signature_image' => 'data:image/png;base64,AAAA',
        ]);

        $log = $this->log();
        $this->assertStringContainsString('"actor":"anonyme"', $log);
        $this->assertStringContainsString('"piece":"fichier facture.pdf (12288 o)"', $log);
        $this->assertStringContainsString('"signature_image":"[masqué]"', $log);
        $this->assertStringContainsString('"note":"'.str_repeat('a', 299).'…"', $log);
    }
}
