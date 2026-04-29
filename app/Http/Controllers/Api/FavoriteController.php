<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Resources\AttractionResource;
use App\Models\Favorite;
use App\Models\Attraction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Throwable;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favoriteAttractions()
            ->apiBase()
            ->withUserFavoriteState((int) $request->user()->id)
            ->paginate(10)
            ->withQueryString();

        return AttractionResource::collection($favorites);
    }

    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        try {
            $favorite = Favorite::firstOrCreate([
                'user_id' => $request->user()->id,
                'attraction_id' => $request->integer('attraction_id'),
            ]);

            return response()->json([
                'data' => $favorite,
            ], $favorite->wasRecentlyCreated ? 201 : 200);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to save favorite right now.',
                'errors' => [
                    'server' => ['An unexpected error occurred.'],
                ],
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
{
    try {
        $deleted = Favorite::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'message' => 'Favorite not found'
            ], 404);
        }

        return response()->noContent();

    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'message' => 'Server error'
        ], 500);
    }
}

}