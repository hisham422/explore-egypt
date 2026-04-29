<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_submit_review_via_regular_form_submit(): void
    {
        $user = User::factory()->create();
        $attraction = $this->createAttraction();

        $response = $this
            ->actingAs($user)
            ->from('/attractions/'.$attraction->id)
            ->post('/reviews', [
                'attraction_id' => $attraction->id,
                'rating' => 5,
                'comment' => 'Wonderful experience',
            ]);

        $response
            ->assertRedirect('/attractions/'.$attraction->id)
            ->assertSessionHas('review_status');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
            'rating' => 5,
            'comment' => 'Wonderful experience',
        ]);
    }

    public function test_authenticated_user_can_submit_review_via_web_route(): void
    {
        $user = User::factory()->create();
        $attraction = $this->createAttraction();

        $response = $this
            ->actingAs($user)
            ->post('/reviews', [
                'attraction_id' => $attraction->id,
                'rating' => 5,
                'comment' => 'Amazing attraction',
            ], [
                'Accept' => 'application/json',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.comment', 'Amazing attraction');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'attraction_id' => $attraction->id,
            'rating' => 5,
            'comment' => 'Amazing attraction',
        ]);
    }

    public function test_guest_cannot_submit_review_via_web_route(): void
    {
        $attraction = $this->createAttraction();

        $response = $this
            ->post('/reviews', [
                'attraction_id' => $attraction->id,
                'rating' => 4,
                'comment' => 'Great place',
            ], [
                'Accept' => 'application/json',
            ]);

        $response->assertUnauthorized();
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
            'name' => 'Temple '.fake()->unique()->randomNumber(5),
            'description' => 'Ancient attraction',
            'image' => null,
            'location' => 'Luxor',
            'civilization_id' => $civilization->id,
            'region_id' => $region->id,
        ]);
    }
}
