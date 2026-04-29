<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Models\Attraction;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    public function index(Request $request)
    {
        $query = Attraction::query()->apiBase();

        $query->when($request->filled('civilization_id'), function ($builder) use ($request): void {
            $builder->where('civilization_id', $request->integer('civilization_id'));
        });

        $query->when($request->filled('region_id'), function ($builder) use ($request): void {
            $builder->where('region_id', $request->integer('region_id'));
        });

        $query->search($request->query('search'));

        $query->when($request->query('sort') === 'rating', function ($builder): void {
            $builder->orderByDesc('reviews_avg_rating')->orderByDesc('reviews_count');
        });

        $query->when($request->user(), function ($builder, $user): void {
            $builder->withUserFavoriteState($user->id);
        });

        $attractions = $query->paginate(10)->withQueryString();

        return AttractionResource::collection($attractions);
    }

    public function show(Request $request, Attraction $attraction)
    {
        $attraction = Attraction::query()
            ->apiBase()
            ->with(['reviews.user'])
            ->whereKey($attraction->id)
            ->when($request->user(), function ($builder, $user): void {
                $builder->withUserFavoriteState($user->id);
            })
            ->firstOrFail();

        return new AttractionResource($attraction);
    }
}