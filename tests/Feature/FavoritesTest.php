<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_toggle_favorite(): void
    {
        $user = User::factory()->create();
        $attraction = $this->createAttraction();

        $addResponse = $this
            ->actingAs($user)
            ->postJson(route('favorites.toggle', $attraction));

        $addResponse
            ->assertCreated()
            ->assertJsonPath('status', 'added');

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
        ]);

        $removeResponse = $this
            ->actingAs($user)
            ->postJson(route('favorites.toggle', $attraction));

        $removeResponse
            ->assertOk()
            ->assertJsonPath('status', 'removed');

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
        ]);
    }

    public function test_authenticated_user_can_get_favorites_with_relations(): void
    {
        $user = User::factory()->create();

        $firstAttraction = $this->createAttraction('First Attraction');
        $secondAttraction = $this->createAttraction('Second Attraction');

        $user->favorites()->create(['attraction_id' => $firstAttraction->id]);
        $user->favorites()->create(['attraction_id' => $secondAttraction->id]);

        $response = $this
            ->actingAs($user)
            ->getJson(route('favorites.index'));

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'civilization',
                        'region',
                    ],
                ],
            ]);
    }

    public function test_guest_cannot_toggle_or_view_favorites(): void
    {
        $attraction = $this->createAttraction();

        $this->postJson(route('favorites.toggle', $attraction))
            ->assertUnauthorized();

        $this->getJson(route('favorites.index'))
            ->assertUnauthorized();
    }

    private function createAttraction(string $name = 'Sample Attraction'): Attraction
    {
        $civilization = Civilization::query()->create([
            'name' => 'Ancient Egypt '.fake()->unique()->randomNumber(5),
            'description' => 'Description',
            'image' => null,
        ]);

        $region = Region::query()->create([
            'name' => 'Cairo '.fake()->unique()->randomNumber(5),
            'description' => 'Description',
            'image' => null,
        ]);

        return Attraction::query()->create([
            'name' => $name,
            'description' => 'Attraction description',
            'image' => null,
            'location' => 'Cairo',
            'civilization_id' => $civilization->id,
            'region_id' => $region->id,
        ]);
    }
}
