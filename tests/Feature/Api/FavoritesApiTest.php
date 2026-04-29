<?php

namespace Tests\Feature\Api;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoritesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_and_remove_favorite_via_api(): void
    {
        $user = User::factory()->create();
        $attraction = $this->createAttraction();

        Sanctum::actingAs($user);

        $addResponse = $this->postJson('/api/v1/favorites', [
            'attraction_id' => $attraction->id,
        ]);

        $addResponse
            ->assertCreated()
            ->assertJsonPath('data.attraction_id', $attraction->id);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
        ]);

        $removeResponse = $this->deleteJson('/api/v1/favorites/'.$attraction->id);

        $removeResponse->assertNoContent();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
        ]);
    }

    public function test_authenticated_user_can_list_favorites_via_api(): void
    {
        $user = User::factory()->create();

        $firstAttraction = $this->createAttraction('First Attraction');
        $secondAttraction = $this->createAttraction('Second Attraction');

        $user->favorites()->create(['attraction_id' => $firstAttraction->id]);
        $user->favorites()->create(['attraction_id' => $secondAttraction->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/favorites');

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
                        'is_favorited',
                    ],
                ],
                'meta',
            ]);
    }

    public function test_guest_cannot_access_protected_favorites_api(): void
    {
        $attraction = $this->createAttraction();

        $this->postJson('/api/v1/favorites', [
            'attraction_id' => $attraction->id,
        ])->assertUnauthorized();

        $this->deleteJson('/api/v1/favorites/'.$attraction->id)
            ->assertUnauthorized();

        $this->getJson('/api/v1/favorites')
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
