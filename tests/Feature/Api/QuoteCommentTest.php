<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuoteComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteCommentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_creates_a_comment_for_a_recipient(): void
    {
        $quote = Quote::factory()->create();

        $response = $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/comments", [
            'recipient_label' => 'nikal',
            'content' => 'Vérifier la disponibilité du pneu.',
        ]);

        $response->assertCreated();
        $this->assertSame('nikal', $response->json('recipient_label'));
        $this->assertSame('Pour Nikal', $response->json('recipient_display'));
        $this->assertDatabaseHas('quote_comments', [
            'quote_id' => $quote->id,
            'recipient_label' => 'nikal',
            'content' => 'Vérifier la disponibilité du pneu.',
        ]);
    }

    #[Test]
    public function it_rejects_an_unknown_recipient_label(): void
    {
        $quote = Quote::factory()->create();

        $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/comments", [
            'recipient_label' => 'unknown',
            'content' => 'Test',
        ])->assertStatus(422);
    }

    #[Test]
    public function it_rejects_an_empty_content(): void
    {
        $quote = Quote::factory()->create();

        $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/comments", [
            'recipient_label' => 'jal',
            'content' => '',
        ])->assertStatus(422);
    }

    #[Test]
    public function it_lists_comments_for_a_quote_ordered_by_creation(): void
    {
        $quote = Quote::factory()->create();
        $other = Quote::factory()->create();

        $first = QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'Premier']);
        $second = QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'jal', 'content' => 'Second']);
        QuoteComment::create(['quote_id' => $other->id, 'recipient_label' => 'nikal', 'content' => 'Autre devis']);

        $response = $this->actingAs($this->user)->getJson("/api/quotes/{$quote->id}/comments");

        $response->assertOk();
        $this->assertCount(2, $response->json('comments'));
        $this->assertSame([$first->id, $second->id], collect($response->json('comments'))->pluck('id')->toArray());
    }

    #[Test]
    public function it_reports_a_quote_as_unresolved_once_it_has_comments(): void
    {
        $quote = Quote::factory()->create();
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'A']);

        $response = $this->actingAs($this->user)->getJson("/api/quotes/{$quote->id}/comments");

        $response->assertOk();
        $this->assertFalse($response->json('is_resolved'));
    }

    #[Test]
    public function it_reports_a_quote_without_comments_as_resolved(): void
    {
        $quote = Quote::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/quotes/{$quote->id}/comments");

        $response->assertOk();
        $this->assertTrue($response->json('is_resolved'));
    }

    #[Test]
    public function it_resolves_the_whole_thread_for_a_quote(): void
    {
        $quote = Quote::factory()->create();
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'A']);
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'jal', 'content' => 'B']);

        $response = $this->actingAs($this->user)->patchJson("/api/quotes/{$quote->id}/comments/resolve");

        $response->assertOk();
        $this->assertNotNull($quote->fresh()->comments_resolved_at);
        $this->assertFalse($quote->fresh()->hasOpenComments());
    }

    #[Test]
    public function it_removes_the_quote_from_both_recipient_filters_once_resolved(): void
    {
        $quote = Quote::factory()->create();
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'A']);
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'jal', 'content' => 'B']);

        $this->actingAs($this->user)->patchJson("/api/quotes/{$quote->id}/comments/resolve");

        $result = Quote::openCommentRecipientsByQuote();
        $this->assertArrayNotHasKey($quote->id, $result);
    }

    #[Test]
    public function it_reopens_a_resolved_thread(): void
    {
        $quote = Quote::factory()->create();
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'A']);
        $quote->markCommentsAsResolved();
        $this->assertFalse($quote->fresh()->hasOpenComments());

        $response = $this->actingAs($this->user)->patchJson("/api/quotes/{$quote->id}/comments/reopen");

        $response->assertOk();
        $this->assertNull($quote->fresh()->comments_resolved_at);
        $this->assertTrue($quote->fresh()->hasOpenComments());
    }

    #[Test]
    public function it_puts_the_quote_back_in_the_recipient_filters_once_reopened(): void
    {
        $quote = Quote::factory()->create();
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'A']);
        $quote->markCommentsAsResolved();

        $this->actingAs($this->user)->patchJson("/api/quotes/{$quote->id}/comments/reopen");

        $result = Quote::openCommentRecipientsByQuote();
        $this->assertSame(['nikal'], $result[$quote->id]);
    }

    #[Test]
    public function it_reopens_the_thread_when_a_new_comment_is_posted_after_resolution(): void
    {
        $quote = Quote::factory()->create();
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'A']);
        $quote->markCommentsAsResolved();
        $this->assertFalse($quote->fresh()->hasOpenComments());

        $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/comments", [
            'recipient_label' => 'jal',
            'content' => 'Nouveau sujet',
        ]);

        $this->assertTrue($quote->fresh()->hasOpenComments());
        $result = Quote::openCommentRecipientsByQuote();
        $this->assertEqualsCanonicalizing(['nikal', 'jal'], $result[$quote->id]);
    }

    #[Test]
    public function it_computes_open_comment_recipients_grouped_by_quote(): void
    {
        $quoteA = Quote::factory()->create();
        $quoteB = Quote::factory()->create();
        QuoteComment::create(['quote_id' => $quoteA->id, 'recipient_label' => 'nikal', 'content' => 'A1']);
        QuoteComment::create(['quote_id' => $quoteA->id, 'recipient_label' => 'jal', 'content' => 'A2']);
        QuoteComment::create(['quote_id' => $quoteB->id, 'recipient_label' => 'nikal', 'content' => 'B1']);
        // La création du commentaire a remis comments_resolved_at à null : on reclôt B
        // via une instance fraîche, pour ne pas dépendre de l'état périmé de $quoteB en mémoire.
        $quoteB->fresh()->markCommentsAsResolved();

        $result = Quote::openCommentRecipientsByQuote();

        $this->assertEqualsCanonicalizing(['nikal', 'jal'], $result[$quoteA->id]);
        $this->assertArrayNotHasKey($quoteB->id, $result);
    }

    #[Test]
    public function it_exposes_open_comment_recipients_on_the_atelier_quotes_list(): void
    {
        $quote = Quote::factory()->create();
        QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'jal', 'content' => 'À voir']);

        $response = $this->actingAs($this->user)->get('/atelier');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('quotes.0.open_comment_recipients', ['jal'])
        );
    }

    #[Test]
    public function it_deletes_a_comment(): void
    {
        $quote = Quote::factory()->create();
        $comment = QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'À supprimer']);

        $this->actingAs($this->user)->deleteJson("/api/quote-comments/{$comment->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('quote_comments', ['id' => $comment->id]);
    }

    #[Test]
    public function it_deletes_comments_when_the_quote_is_permanently_deleted(): void
    {
        $quote = Quote::factory()->create();
        $comment = QuoteComment::create(['quote_id' => $quote->id, 'recipient_label' => 'nikal', 'content' => 'Lié au devis']);

        $quote->forceDelete();

        $this->assertDatabaseMissing('quote_comments', ['id' => $comment->id]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $quote = Quote::factory()->create();

        $this->postJson("/api/quotes/{$quote->id}/comments", [
            'recipient_label' => 'nikal',
            'content' => 'Test',
        ])->assertStatus(401);
    }
}
