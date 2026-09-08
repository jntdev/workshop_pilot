<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\User;
use App\Services\Catalogue\CgnFtpSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use ZipArchive;

class CatalogueSyncControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $scratchDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->scratchDir = sys_get_temp_dir().'/cgn_sync_test_'.uniqid();
        mkdir($this->scratchDir);

        Storage::fake('cgn_ftp');
        config(['services.cgn_ftp.remote_path' => 'StockNouveautesCgn.zip']);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->scratchDir.'/*') ?: []);
        @rmdir($this->scratchDir);
        parent::tearDown();
    }

    private function putValidRemoteZip(): void
    {
        $zipPath = $this->scratchDir.'/source.zip';
        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile(base_path('tests/fixtures/cgn_test_import.csv'), 'StockNouveautesCgn022252.csv');
        $zip->close();

        Storage::disk('cgn_ftp')->put('StockNouveautesCgn.zip', file_get_contents($zipPath));
        unlink($zipPath);
    }

    #[Test]
    public function it_downloads_extracts_and_imports_the_catalogue_successfully(): void
    {
        $this->putValidRemoteZip();
        $service = new CgnFtpSyncService($this->scratchDir);

        $result = $service->sync();

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['imported_at']);
        $this->assertFileExists($this->scratchDir.'/StockNouveautesCgn022252.csv');
        $this->assertNotNull(Article::where('reference', '435447')->first());
    }

    #[Test]
    public function it_includes_the_real_article_count_delta_in_the_success_message(): void
    {
        $countBefore = Article::count();
        $this->putValidRemoteZip();
        $service = new CgnFtpSyncService($this->scratchDir);

        $result = $service->sync();

        $expectedCreated = Article::count() - $countBefore;
        $this->assertStringContainsString((string) $expectedCreated, $result['message']);
        $this->assertStringContainsString((string) Article::count(), $result['message']);
    }

    #[Test]
    public function it_fails_cleanly_when_the_remote_file_does_not_exist(): void
    {
        $service = new CgnFtpSyncService($this->scratchDir);

        $result = $service->sync();

        $this->assertFalse($result['success']);
        $this->assertNull($result['imported_at']);
        $this->assertNotEmpty($result['message']);
    }

    #[Test]
    public function it_fails_cleanly_when_the_remote_archive_is_corrupt(): void
    {
        Storage::disk('cgn_ftp')->put('StockNouveautesCgn.zip', 'not a real zip file');
        $service = new CgnFtpSyncService($this->scratchDir);

        $result = $service->sync();

        $this->assertFalse($result['success']);
        $this->assertNull($result['imported_at']);
    }

    #[Test]
    public function it_fails_cleanly_when_the_archive_contains_no_csv(): void
    {
        $zipPath = $this->scratchDir.'/empty.zip';
        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('readme.txt', 'no csv here');
        $zip->close();

        Storage::disk('cgn_ftp')->put('StockNouveautesCgn.zip', file_get_contents($zipPath));
        unlink($zipPath);

        $service = new CgnFtpSyncService($this->scratchDir);
        $result = $service->sync();

        $this->assertFalse($result['success']);
        $this->assertNull($result['imported_at']);
    }

    #[Test]
    public function it_never_leaves_any_temporary_file_behind_regardless_of_outcome(): void
    {
        $before = glob(sys_get_temp_dir().'/cgn_sync_*') ?: [];

        $this->putValidRemoteZip();
        (new CgnFtpSyncService($this->scratchDir))->sync();

        Storage::disk('cgn_ftp')->delete('StockNouveautesCgn.zip');
        (new CgnFtpSyncService($this->scratchDir))->sync();

        $after = glob(sys_get_temp_dir().'/cgn_sync_*') ?: [];
        $this->assertSame($before, $after, 'Des fichiers temporaires cgn_sync_* orphelins ont été laissés sur le disque.');
    }

    #[Test]
    public function it_never_writes_outside_the_destination_directory_even_with_a_nested_path_inside_the_archive(): void
    {
        $zipPath = $this->scratchDir.'/nested.zip';
        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile(base_path('tests/fixtures/cgn_test_import.csv'), '../escaped/StockNouveautesCgn022252.csv');
        $zip->close();

        Storage::disk('cgn_ftp')->put('StockNouveautesCgn.zip', file_get_contents($zipPath));
        unlink($zipPath);

        $result = (new CgnFtpSyncService($this->scratchDir))->sync();

        $this->assertTrue($result['success']);
        $this->assertFileExists($this->scratchDir.'/StockNouveautesCgn022252.csv');
        $this->assertDirectoryDoesNotExist($this->scratchDir.'/escaped');
        $this->assertDirectoryDoesNotExist(dirname($this->scratchDir).'/escaped');
    }

    #[Test]
    public function post_route_returns_200_and_the_service_payload_on_success(): void
    {
        $this->putValidRemoteZip();
        $this->app->bind(CgnFtpSyncService::class, fn () => new CgnFtpSyncService($this->scratchDir));

        $response = $this->actingAs($this->user)->postJson('/api/catalogue/sync-cgn');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'imported_at']);
    }

    #[Test]
    public function post_route_returns_502_and_a_clear_message_on_failure(): void
    {
        $this->app->bind(CgnFtpSyncService::class, fn () => new CgnFtpSyncService($this->scratchDir));

        $response = $this->actingAs($this->user)->postJson('/api/catalogue/sync-cgn');

        $response->assertStatus(502)
            ->assertJsonPath('success', false)
            ->assertJsonPath('imported_at', null);
        $this->assertNotEmpty($response->json('message'));
    }

    #[Test]
    public function post_route_requires_authentication(): void
    {
        $response = $this->postJson('/api/catalogue/sync-cgn');

        $response->assertUnauthorized();
    }
}
