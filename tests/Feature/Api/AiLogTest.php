<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiLogTest extends TestCase
{
    private const TOKEN = 'secret-ai-token';

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir().'/ai-logs-'.uniqid();
        File::makeDirectory($this->dir);
        config(['ai.token' => self::TOKEN, 'ai.logs_path' => $this->dir]);

        file_put_contents($this->dir.'/laravel.log', implode("\n", [
            '[2026-11-08 10:00:00] production.INFO: ai-api {"method":"GET","path":"api/ai/quotes/613","ip":"1.2.3.4"} ',
            '[2026-11-09 22:37:36] production.INFO: ai-api {"method":"PATCH","path":"api/ai/quotes/613/status","ip":"1.2.3.4"} ',
            '[2026-11-10 08:15:00] production.ERROR: SQLSTATE[23000]: Integrity constraint violation {"exception":"[object] (PDOException(code: 23000))',
            '[stacktrace]',
            '#0 /app/vendor/laravel/framework/src/Illuminate/Database/Connection.php(580): PDOStatement->execute()',
            '#1 {main}',
            '"} ',
            '[2026-11-12 09:00:00] production.WARNING: Valeur malformée ignorée ',
            '',
        ]));
        file_put_contents($this->dir.'/laravel-2026-11-11.log', '[2026-11-11 12:00:00] production.ERROR: Erreur du fichier quotidien '."\n");
        file_put_contents($this->dir.'/autre.txt', '[2026-11-13 12:00:00] production.ERROR: pas un journal Laravel'."\n");
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dir);
        parent::tearDown();
    }

    /**
     * @return array<string, string>
     */
    private function auth(): array
    {
        return ['Authorization' => 'Bearer '.self::TOKEN];
    }

    #[Test]
    public function it_requires_the_ai_token(): void
    {
        $this->getJson('/api/ai/logs')->assertUnauthorized();
    }

    #[Test]
    public function it_lists_entries_newest_first_across_log_files(): void
    {
        $response = $this->getJson('/api/ai/logs', $this->auth())->assertOk();

        $this->assertSame(
            ['2026-11-12 09:00:00', '2026-11-11 12:00:00', '2026-11-10 08:15:00', '2026-11-09 22:37:36', '2026-11-08 10:00:00'],
            array_column($response->json('entries'), 'date'),
        );
        $this->assertEqualsCanonicalizing(['laravel.log', 'laravel-2026-11-11.log'], array_column($response->json('files'), 'name'));
        $response->assertJsonPath('truncated', false)
            ->assertJsonPath('entries.0.level', 'warning')
            ->assertJsonPath('entries.1.file', 'laravel-2026-11-11.log');
    }

    #[Test]
    public function it_keeps_the_stack_trace_of_an_exception(): void
    {
        $entry = collect($this->getJson('/api/ai/logs?level=error', $this->auth())->json('entries'))
            ->firstWhere('date', '2026-11-10 08:15:00');

        $this->assertStringStartsWith('SQLSTATE[23000]', $entry['message']);
        $this->assertSame('[stacktrace]', $entry['trace'][0]);
        $this->assertSame(4, $entry['trace_lines']);
    }

    #[Test]
    public function it_filters_by_text_level_and_period(): void
    {
        $this->getJson('/api/ai/logs?search=QUOTES/613/STATUS', $this->auth())
            ->assertOk()
            ->assertJsonCount(1, 'entries')
            ->assertJsonPath('entries.0.date', '2026-11-09 22:37:36');

        $this->getJson('/api/ai/logs?search=Connection.php', $this->auth())
            ->assertJsonCount(1, 'entries')
            ->assertJsonPath('entries.0.date', '2026-11-10 08:15:00');

        $this->getJson('/api/ai/logs?level=error', $this->auth())
            ->assertJsonCount(2, 'entries');

        $response = $this->getJson('/api/ai/logs?from=2026-11-09&to=2026-11-10', $this->auth())->assertOk();
        $this->assertSame(['2026-11-10 08:15:00', '2026-11-09 22:37:36'], array_column($response->json('entries'), 'date'));
    }

    #[Test]
    public function it_limits_the_number_of_entries(): void
    {
        $this->getJson('/api/ai/logs?limit=2', $this->auth())
            ->assertJsonCount(2, 'entries')
            ->assertJsonPath('entries.0.date', '2026-11-12 09:00:00')
            ->assertJsonPath('entries.1.date', '2026-11-11 12:00:00');
    }

    #[Test]
    public function it_reads_large_files_across_chunk_boundaries(): void
    {
        $lines = [];
        for ($i = 0; $i < 3000; $i++) {
            $date = date('Y-m-d H:i:s', strtotime('2026-10-01 00:00:00') + $i * 60);
            $lines[] = "[$date] production.INFO: entrée $i ".str_repeat('x', $i % 7 === 0 ? 70000 : $i % 300);
            if ($i % 11 === 0) {
                $lines[] = '#0 trace de '.$i;
            }
        }
        file_put_contents($this->dir.'/laravel.log', implode("\n", $lines)."\n");
        File::delete($this->dir.'/laravel-2026-11-11.log');

        $entries = $this->getJson('/api/ai/logs?limit=500', $this->auth())->assertOk()->json('entries');

        $this->assertCount(500, $entries);
        foreach ($entries as $k => $entry) {
            $i = 2999 - $k;
            $this->assertStringStartsWith("entrée $i ", $entry['message']);
            $this->assertSame($i % 11 === 0 ? ['#0 trace de '.$i] : [], $entry['trace']);
        }
        $this->assertLessThanOrEqual(2001, mb_strlen($entries[0]['message']));
    }

    #[Test]
    public function it_validates_parameters(): void
    {
        $this->getJson('/api/ai/logs?level=fatal', $this->auth())->assertUnprocessable();
        $this->getJson('/api/ai/logs?limit=501', $this->auth())->assertUnprocessable();
        $this->getJson('/api/ai/logs?from=2026-11-10&to=2026-11-09', $this->auth())->assertUnprocessable();
    }

    #[Test]
    public function it_returns_an_empty_list_without_log_files(): void
    {
        config(['ai.logs_path' => $this->dir.'/absent']);

        $this->getJson('/api/ai/logs', $this->auth())
            ->assertOk()
            ->assertJsonPath('files', [])
            ->assertJsonPath('entries', []);
    }
}
