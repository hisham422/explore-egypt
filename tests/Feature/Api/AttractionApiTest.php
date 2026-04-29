<?php

namespace Tests\Feature\Api;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Favorite;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttractionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_attractions_index_is_paginated(): void
    {
        $this->seedAttractions(12);

        $response = $this->getJson('/api/v1/attractions');

        $response
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 12)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_attractions_index_supports_search_by_name_and_description(): void
    {
        [$civilization, $region] = $this->createBaseTaxonomy();

        Attraction::query()->create([
            'name' => 'Pyramid Complex',
            'description' => 'Ancient site in Giza',
            'image' => null,
            'location' => 'Giza',
            'civilization_id' => $civilization->id,
            'region_id' => $region->id,
        ]);

        Attraction::query()->create([
            'name' => 'Karnak Temple',
            'description' => 'Massive pyramid-inspired columns',
            'image' => null,
            'location' => 'Luxor',
            'civilization_id' => $civilization->id,
            'region_id' => $region->id,
        ]);

        Attraction::query()->create([
            'name' => 'Citadel of Cairo',
            'description' => 'Historic fortress in Cairo',
            'image' => null,
            'location' => 'Cairo',
            'civilization_id' => $civilization->id,
            'region_id' => $region->id,
        ]);

        $response = $this->getJson('/api/v1/attractions?search=pyramid');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_civilization_attractions_endpoint_is_paginated(): void
    {
        [$civilization, $region] = $this->createBaseTaxonomy();

        for ($index = 1; $index <= 11; $index++) {
            Attraction::query()->create([
                'name' => 'Civilization Attraction '.$index,
                'description' => 'Description '.$index,
                'image' => null,
                'location' => 'Location '.$index,
                'civilization_id' => $civilization->id,
                'region_id' => $region->id,
            ]);
        }

        $response = $this->getJson('/api/v1/civilizations/'.$civilization->id.'/attractions');

        $response
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 11);
    }

    public function test_region_attractions_endpoint_is_paginated(): void
    {
        [$civilization, $region] = $this->createBaseTaxonomy();

        for ($index = 1; $index <= 13; $index++) {
            Attraction::query()->create([
                'name' => 'Region Attraction '.$index,
                'description' => 'Description '.$index,
                'image' => null,
                'location' => 'Location '.$index,
                'civilization_id' => $civilization->id,
                'region_id' => $region->id,
            ]);
        }

        $response = $this->getJson('/api/v1/regions/'.$region->id.'/attractions');

        $response
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 13);
    }

    public function test_attractions_response_includes_is_favorited_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        [$civilization, $region] = $this->createBaseTaxonomy();

        $favoritedAttraction = Attraction::query()->create([
            'name' => 'Abu Simbel',
            'description' => 'Rock temples in Aswan',
            'image' => null,
            'location' => 'Aswan',
            'civilization_id' => $civilization->id,
            'region_id' => $region->id,
        ]);

        $notFavoritedAttraction = Attraction::query()->create([
            'name' => 'Alexandria Library',
            'description' => 'Modern cultural landmark',
            'image' => null,
            'location' => 'Alexandria',
            'civilization_id' => $civilization->id,
            'region_id' => $region->id,
        ]);

        Favorite::query()->create([
            'user_id' => $user->id,
            'attraction_id' => $favoritedAttraction->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson('/api/v1/attractions');

        $response->assertOk();

        $itemsById = collect($response->json('data'))->keyBy('id');

        $this->assertTrue($itemsById[$favoritedAttraction->id]['is_favorited']);
        $this->assertFalse($itemsById[$notFavoritedAttraction->id]['is_favorited']);
    }

    public function test_versioned_attractions_endpoint_is_available(): void
    {
        $this->seedAttractions(2);

        $response = $this->getJson('/api/v1/attractions');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    private function seedAttractions(int $count): void
    {
        [$civilization, $region] = $this->createBaseTaxonomy();

        for ($index = 1; $index <= $count; $index++) {
            Attraction::query()->create([
                'name' => 'Attraction '.$index,
                'description' => 'Description '.$index,
                'image' => null,
                'location' => 'Location '.$index,
                'civilization_id' => $civilization->id,
                'region_id' => $region->id,
            ]);
        }
    }

    /**
     * @return array{Civilization, Region}
     */
    private function createBaseTaxonomy(): array
    {
        $civilization = Civilization::query()->create([
            'name' => 'Ancient Egypt '.fake()->unique()->randomNumber(5),
            'description' => 'The civilization of the pharaohs',
            'image' => null,
        ]);

        $region = Region::query()->create([
            'name' => 'Giza '.fake()->unique()->randomNumber(5),
            'description' => 'A major tourism governorate',
            'image' => null,
        ]);

        return [$civilization, $region];
    }
}
