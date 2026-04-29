<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Favorite;
use App\Http\Requests\StoreFavoriteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()
            ->favoriteAttractions()
            ->with(['civilization', 'region'])
            ->latest('favorites.id')
            ->get();

        return response()->json([
            'data' => $favorites
        ]);
    }

    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $attractionId = $request->integer('attraction_id');

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'attraction_id' => $attractionId,
        ]);

        $favoritesCount = Favorite::query()
            ->where('attraction_id', $attractionId)
            ->count();

        return response()->json([
            'status' => $favorite->wasRecentlyCreated ? 'added' : 'existing',
            'message' => $favorite->wasRecentlyCreated ? 'Added to favorites' : 'Already in favorites',
            'favorite_id' => $favorite->id,
            'attraction_id' => $attractionId,
            'favorites_count' => $favoritesCount,
        ], $favorite->wasRecentlyCreated ? 201 : 200);
    }

    public function toggle(Request $request, Attraction $attraction): JsonResponse
    {
        $favorite = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->where('attraction_id', $attraction->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $status = 'removed';
            $statusCode = 200;
        } else {
            Favorite::create([
                'user_id' => $request->user()->id,
                'attraction_id' => $attraction->id,
            ]);
            $status = 'added';
            $statusCode = 201;
        }

        $favoritesCount = Favorite::query()
            ->where('attraction_id', $attraction->id)
            ->count();

        return response()->json([
            'status' => $status,
            'message' => $status === 'added' ? 'Added to favorites' : 'Removed from favorites',
            'attraction_id' => $attraction->id,
            'favorites_count' => $favoritesCount,
        ], $statusCode);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $favorite = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->whereKey($id)
            ->firstOrFail();

        $attractionId = $favorite->attraction_id;

        $favorite->delete();

        $favoritesCount = Favorite::query()
            ->where('attraction_id', $attractionId)
            ->count();

        return response()->json([
            'status' => 'removed',
            'message' => 'Removed from favorites',
            'attraction_id' => $attractionId,
            'favorites_count' => $favoritesCount,
        ]);
    }
}