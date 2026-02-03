<?php

namespace Tests\Feature;

use App\Livewire\Counter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_counter_component_renders(): void
    {
        Livewire::test(Counter::class)
            ->assertStatus(200)
            ->assertSee('Compteur Livewire');
    }

    public function test_counter_starts_at_zero(): void
    {
        Livewire::test(Counter::class)
            ->assertSet('count', 0);
    }

    public function test_counter_can_increment(): void
    {
        Livewire::test(Counter::class)
            ->assertSet('count', 0)
            ->call('increment')
            ->assertSet('count', 1)
            ->call('increment')
            ->assertSet('count', 2);
    }

    public function test_counter_can_decrement(): void
    {
        Livewire::test(Counter::class)
            ->assertSet('count', 0)
            ->call('decrement')
            ->assertSet('count', -1)
            ->call('decrement')
            ->assertSet('count', -2);
    }

    public function test_counter_demo_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->withoutVite()->get('/counter');

        $response->assertStatus(200)
            ->assertSee('Livewire Demo');
    }
}
