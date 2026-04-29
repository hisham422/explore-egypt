<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Http\Resources\CivilizationResource;
use App\Models\Civilization;
use Illuminate\Http\Request;

class CivilizationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        return CivilizationResource::collection(
            Civilization::query()->orderBy('name')->paginate($perPage)->withQueryString()
        );
    }

    public function attractions(Request $request, Civilization $civilization)
    {
        $attractions = $civilization->attractions()
            ->apiBase()
            ->when($request->user(), function ($builder, $user): void {
                $builder->withUserFavoriteState($user->id);
            })
            ->paginate(10)
            ->withQueryString();

        return AttractionResource::collection($attractions);
    }
    public function show(Civilization $civilization)
{
    return new CivilizationResource($civilization);
}
}