<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        return RegionResource::collection(
            Region::query()->orderBy('name')->paginate($perPage)->withQueryString()
        );
    }

    public function attractions(Request $request, Region $region)
    {
        $attractions = $region->attractions()
            ->apiBase()
            ->when($request->user(), function ($builder, $user): void {
                $builder->withUserFavoriteState($user->id);
            })
            ->paginate(10)
            ->withQueryString();

        return AttractionResource::collection($attractions);
    }
    public function show(Region $region)
{
    return new RegionResource($region);
}
}