<?php

namespace Tests\Feature\Api;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Region;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_review(): void
    {
        $user = User::factory()->create();
        $attraction = $this->createAttraction();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/reviews', [
                'attraction_id' => $attraction->id,
                'rating' => 5,
                'comment' => 'Fantastic place to visit',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Review created successfully')
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.comment', 'Fantastic place to visit');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
            'rating' => 5,
            'comment' => 'Fantastic place to visit',
        ]);
    }

    public function test_authenticated_user_updates_existing_review_for_same_attraction(): void
    {
        $user = User::factory()->create();
        $attraction = $this->createAttraction();

        Review::query()->create([
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
            'rating' => 2,
            'comment' => 'It was crowded',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/reviews', [
                'attraction_id' => $attraction->id,
                'rating' => 4,
                'comment' => 'Much better on the second visit',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Review updated successfully')
            ->assertJsonPath('data.rating', 4)
            ->assertJsonPath('data.comment', 'Much better on the second visit');

        $this->assertDatabaseCount('reviews', 1);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
            'rating' => 4,
            'comment' => 'Much better on the second visit',
        ]);
    }

    public function test_guest_cannot_submit_review(): void
    {
        $attraction = $this->createAttraction();

        $response = $this->postJson('/api/v1/reviews', [
            'attraction_id' => $attraction->id,
            'rating' => 5,
        ]);

        $response->assertUnauthorized();
    }

    public function test_review_requires_rating_in_valid_range(): void
    {
        $user = User::factory()->create();
        $attraction = $this->createAttraction();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/reviews', [
                'attraction_id' => $attraction->id,
                'rating' => 6,
                'comment' => 'Invalid value',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['rating']);
    }

    private function createAttraction(): Attraction
    {
        $civilization = Civilization::query()->create([
            'name' => 'Ancient Egypt '.fake()->unique()->randomNumber(5),
            'description' => 'Historic civilization',
            'image' => null,
        ]);

        $region = Region::query()->create([
            'name' => 'Luxor '.fake()->unique()->randomNumber(5),
            'description' => 'Historic region',
            'image' => null,
        ]);

        return Attraction::query()->create([
            'name' => 'Karnak Temple '.fake()->unique()->randomNumber(5),
            'description' => 'Ancient temple complex',
            'image' => null,
            'location' => 'Luxor',
            'civilization_id' => $civilization->id,
            'region_id' => $region->id,
        ]);
    }
}
