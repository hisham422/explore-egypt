<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Region;
use App\Models\SiteSetting;
use App\Support\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TourismController extends Controller
{
    public function home(): View
    {
        $civilizations = Cache::remember('home_civilizations', 60, function () {
            return Civilization::latest()->take(4)->get();
        });

        $regions = Cache::remember('home_regions', 60, function () {
            return Region::latest()->take(4)->get();
        });

        $featuredAttractionsQuery = Attraction::query()
            ->apiBase()
            ->take(3);

        if (Auth::check()) {
            $featuredAttractionsQuery->withUserFavoriteState(Auth::id());
        }

        $featuredAttractions = $featuredAttractionsQuery->get();

        $heroImage = SiteSetting::getValue('home_hero_image');
        $heroImageUrl = ImageManager::publicUrl($heroImage, 'Explore Egypt', '1600x900');

        return view('tourism.home', compact(
            'civilizations',
            'regions',
            'featuredAttractions',
            'heroImage',
            'heroImageUrl'
        ));
    }

    public function civilizations(): View
    {
        $civilizations = Civilization::latest()->paginate(12);

        return view('tourism.civilizations.index', compact('civilizations'));
    }

    public function civilization(Civilization $civilization): View
    {
        $query = $civilization->attractions()->apiBase();

        if (Auth::check()) {
            $query->withUserFavoriteState(Auth::id());
        }

        $attractions = $query->paginate(12)->withQueryString();

        return view('tourism.civilizations.show', compact(
            'civilization',
            'attractions'
        ));
    }

    public function regions(): View
    {
        $regions = Region::latest()->paginate(12);

        return view('tourism.regions.index', compact('regions'));
    }

    public function region(Region $region): View
    {
        $query = $region->attractions()->apiBase();

        if (Auth::check()) {
            $query->withUserFavoriteState(Auth::id());
        }

        $attractions = $query->paginate(12)->withQueryString();

        return view('tourism.regions.show', compact(
            'region',
            'attractions'
        ));
    }

    public function attraction(Attraction $attraction): View
    {
        $query = Attraction::query()
            ->apiBase()
            ->whereKey($attraction->id)
            ->with([
                'images',
                'reviews' => fn ($q) => $q->with('user')->latest(),
            ]);

        if (Auth::check()) {
            $query->withUserFavoriteState(Auth::id());
        }

        $attraction = $query->firstOrFail();
        $isFavorited = (bool) ($attraction->is_favorited ?? false);

        return view('tourism.attractions.show', compact(
            'attraction',
            'isFavorited'
        ));
    }

    public function explore(Request $request): View
    {
        $query = Attraction::query()
            ->apiBase()
            ->search($request->string('search')->toString())
            ->when($request->filled('civilization_id'), function ($builder) use ($request) {
                $builder->where('civilization_id', $request->civilization_id);
            })
            ->when($request->filled('region_id'), function ($builder) use ($request) {
                $builder->where('region_id', $request->region_id);
            });

        if ($request->sort === 'rating') {
            $query->orderByDesc('reviews_avg_rating')
                  ->orderByDesc('reviews_count');
        }

        if (Auth::check()) {
            $query->withUserFavoriteState(Auth::id());
        }

        $attractions = $query->paginate(12)->withQueryString();

        return view('tourism.attractions.index', [
            'attractions' => $attractions,
            'search' => $request->search,
            'civilizations' => Civilization::orderBy('name')->get(),
            'regions' => Region::orderBy('name')->get(),
            'filters' => [
                'civilization_id' => $request->civilization_id,
                'region_id' => $request->region_id,
                'sort' => $request->sort,
            ],
        ]);
    }
}