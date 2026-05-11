<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Region;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\AttractionView;
use App\Models\Favorite;

class TourismController extends Controller
{
    public function home(): View
    {
        $civilizations = Cache::remember('home_civilizations', 60, function () {
            return Civilization::latest()->take(4)->get();
        });

        $popularAttractionsQuery = Attraction::query()
            ->apiBase()
            ->orderByDesc('reviews_count')
            ->orderByDesc('reviews_avg_rating')
            ->take(6);

        if (Auth::check()) {
            $popularAttractionsQuery->withUserFavoriteState(Auth::id());
        }

        $popularAttractions = $popularAttractionsQuery->get();

        $recommendedAttractions = collect();
        $recentlyViewed = collect();

        if (Auth::check()) {
            /** @var User $authUser */
            $authUser = Auth::user();

            $favoriteAttractions = $authUser
                ->favoriteAttractions()
                ->select('attractions.id', 'civilization_id', 'region_id')
                ->get();

            $favoriteAttractionIds = $favoriteAttractions->pluck('id')->values();
            $favoriteCivilizationIds = $favoriteAttractions->pluck('civilization_id')->filter()->unique()->values();
            $favoriteRegionIds = $favoriteAttractions->pluck('region_id')->filter()->unique()->values();

            // Build improved recommendations using favorites + recent views + co-favorites
            $recentViewedIds = AttractionView::query()
                ->where('user_id', Auth::id())
                ->latest()
                ->limit(20)
                ->pluck('attraction_id')
                ->unique()
                ->values();

            $excludeIds = $favoriteAttractionIds->merge($recentViewedIds)->unique()->values();

            // Find co-favorite attractions (what users who liked the same items also liked)
            $coFavUsers = Favorite::query()
                ->whereIn('attraction_id', $favoriteAttractionIds->all())
                ->where('user_id', '!=', Auth::id())
                ->pluck('user_id')
                ->unique()
                ->values();

            $coFavoriteCounts = collect();

            if ($coFavUsers->isNotEmpty()) {
                $coFavoriteCounts = Favorite::query()
                    ->select('attraction_id', DB::raw('count(*) as cnt'))
                    ->whereIn('user_id', $coFavUsers->all())
                    ->whereNotIn('attraction_id', $excludeIds->all())
                    ->groupBy('attraction_id')
                    ->orderByDesc('cnt')
                    ->limit(200)
                    ->get()
                    ->pluck('cnt', 'attraction_id');
            }

            // Candidate attractions: same civ/region OR co-favorited by similar users
            $candidatesQuery = Attraction::query()->apiBase()->whereNotIn('id', $excludeIds->all());

            $candidatesQuery->where(function ($q) use ($favoriteCivilizationIds, $favoriteRegionIds, $coFavoriteCounts) {
                if ($favoriteCivilizationIds->isNotEmpty()) {
                    $q->whereIn('civilization_id', $favoriteCivilizationIds->all());
                }

                if ($favoriteRegionIds->isNotEmpty()) {
                    $method = $favoriteCivilizationIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                    $q->{$method}('region_id', $favoriteRegionIds->all());
                }

                if ($coFavoriteCounts->isNotEmpty()) {
                    $q->orWhereIn('id', $coFavoriteCounts->keys()->all());
                }
            });

            $candidates = $candidatesQuery->withUserFavoriteState(Auth::id())->get()->keyBy('id');

            if ($candidates->isEmpty() && $recentViewedIds->isNotEmpty()) {
                // Fallback to suggestions based on last viewed attraction
                $lastView = AttractionView::query()->where('user_id', Auth::id())->latest()->first();
                if ($lastView) {
                    $pivot = Attraction::find($lastView->attraction_id);
                    if ($pivot) {
                        $candidates = Attraction::query()
                            ->apiBase()
                            ->whereNotIn('id', [$pivot->id])
                            ->where(function ($b) use ($pivot) {
                                $b->where('region_id', $pivot->region_id)
                                  ->orWhere('civilization_id', $pivot->civilization_id);
                            })
                            ->withUserFavoriteState(Auth::id())
                            ->limit(50)
                            ->get()
                            ->keyBy('id');
                    }
                }
            }

            // Score candidates
            $scored = collect();

            foreach ($candidates as $id => $attr) {
                $score = 0;

                if ($favoriteCivilizationIds->contains($attr->civilization_id)) {
                    $score += 40;
                }

                if ($favoriteRegionIds->contains($attr->region_id)) {
                    $score += 30;
                }

                $coCount = (int) ($coFavoriteCounts[$id] ?? 0);
                $score += $coCount * 20;

                $score += (float) ($attr->reviews_avg_rating ?? 0) * 5;
                $score += min(50, (int) ($attr->reviews_count ?? 0) );

                $scored->put($id, ['score' => $score, 'attraction' => $attr]);
            }

            $recommendedAttractions = $scored->sortByDesc('score')->values()->map(fn ($v) => $v['attraction'])->take(6);

            // Ensure favorite state is present on returned models
            if ($recommendedAttractions->isNotEmpty()) {
                // already loaded with withUserFavoriteState on candidate queries
            }
        }

        $heroImage = SiteSetting::getValue('home_hero_image');
        $heroImageUrl = asset('media/hero/home-hero.png');
        $heroVideoUrl = asset('media/hero/home-hero-video.mp4');

        $beachAttractions = Attraction::query()
            ->apiBase()
            ->beaches()
            ->take(3)
            ->get();

        $historicalAttractions = Attraction::query()
            ->apiBase()
            ->historical()
            ->take(3)
            ->get();

        $activityAttractions = Attraction::query()
            ->apiBase()
            ->activities()
            ->take(3)
            ->get();

        $coastalAttractions = Attraction::query()
            ->apiBase()
            ->coastal()
            ->take(3)
            ->get();

        $summerRecommendations = Attraction::query()
            ->apiBase()
            ->whereIn('type', [Attraction::TYPE_BEACH, Attraction::TYPE_COASTAL])
            ->orderByDesc('reviews_avg_rating')
            ->take(6)
            ->get();

        return view('tourism.home', compact(
            'civilizations',
            'popularAttractions',
            'recommendedAttractions',
            'recentlyViewed',
            'heroImage',
            'heroImageUrl',
            'heroVideoUrl',
            'beachAttractions',
            'historicalAttractions',
            'activityAttractions',
            'coastalAttractions',
            'summerRecommendations'
        ));
    }

    public function civilizations(): View
    {
        $civilizations = Civilization::latest()->paginate(12);

        return view('tourism.civilizations.index', compact('civilizations'));
    }

    public function civilization(Civilization $civilization): View
    {
        $withFavoriteState = Auth::check();

        $civilization->load([
            'periods' => fn ($query) => $query->orderBy('start_year')->orderBy('sort_order'),
            'periods.attractions' => function ($query) use ($withFavoriteState): void {
                $query->apiBase();

                if ($withFavoriteState) {
                    $query->withUserFavoriteState(Auth::id());
                }
            },
        ]);

        $query = $civilization->attractions()->apiBase();

        if (Auth::check()) {
            $query->withUserFavoriteState(Auth::id());
        }

        $heroVideoUrl = $civilization->hero_video_url
            ? (Str::startsWith($civilization->hero_video_url, ['http://', 'https://'])
                ? $civilization->hero_video_url
                : asset('storage/'.$civilization->hero_video_url))
            : asset('media/hero/home-hero-video.mp4');

        $attractions = $query->paginate(12)->withQueryString();

        return view('tourism.civilizations.show', compact(
            'civilization',
            'heroVideoUrl',
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

        // Log view for authenticated users to enable personalization
        if (Auth::check()) {
            try {
                AttractionView::create([
                    'user_id' => Auth::id(),
                    'attraction_id' => $attraction->id,
                ]);
            } catch (\Exception $e) {
                // non-fatal: ignore logging errors
            }
        }

        return view('tourism.attractions.show', compact(
            'attraction',
            'isFavorited'
        ));
    }




    public function explore(Request $request): View
    {
        $selectedType = $request->string('type')->toString();

        if (! in_array($selectedType, Attraction::TYPES, true)) {
            $selectedType = '';
        }

        $query = Attraction::query()
            ->apiBase()
            ->search($request->string('search')->toString())
            ->when($selectedType !== '', function ($builder) use ($selectedType) {
                $builder->where('type', $selectedType);
            })
            ->when($request->filled('civilization_id'), function ($builder) use ($request) {
                $builder->where('civilization_id', $request->civilization_id);
            })
            ->when($request->filled('region_id'), function ($builder) use ($request) {
                $builder->where('region_id', $request->region_id);
            })
            ->when($request->filled('min_rating'), function ($builder) use ($request) {
                $builder->having('reviews_avg_rating', '>=', (float) $request->min_rating);
            });

        if ($request->sort === 'rating') {
            $query->orderByDesc('reviews_avg_rating')
                  ->orderByDesc('reviews_count');
        }

        if (Auth::check()) {
            $query->withUserFavoriteState(Auth::id());
        }

        $mapAttractions = (clone $query)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->limit(250)
            ->get(['id', 'name', 'description', 'image', 'type', 'location', 'lat', 'lng', 'civilization_id', 'region_id', 'reviews_avg_rating', 'reviews_count'])
            ->map(function (Attraction $attraction): array {
                $markerType = $this->resolveMapMarkerType($attraction);

                return [
                    'id' => $attraction->id,
                    'name' => $attraction->name,
                    'location' => $attraction->location,
                    'lat' => (float) $attraction->lat,
                    'lng' => (float) $attraction->lng,
                    'rating' => (float) ($attraction->average_rating ?? 0),
                    'reviews_count' => (int) ($attraction->reviews_count ?? 0),
                    'is_favorited' => (bool) ($attraction->is_favorited ?? false),
                    'civilization_id' => (int) ($attraction->civilization_id ?? 0),
                    'region_id' => (int) ($attraction->region_id ?? 0),
                    'image' => $attraction->image
                        ? (Str::startsWith($attraction->image, ['http://', 'https://'])
                            ? $attraction->image
                            : asset('storage/' . $attraction->image))
                        : null,
                    'marker_type' => $markerType,
                    'url' => route('attractions.show', $attraction),
                ];
            })
            ->values();

        $attractions = $query->paginate(12)->withQueryString();

        return view('tourism.attractions.index', [
            'attractions' => $attractions,
            'mapAttractions' => $mapAttractions,
            'search' => $request->search,
            'civilizations' => Civilization::orderBy('name')->get(),
            'regions' => Region::orderBy('name')->get(),
            'filters' => [
                'type' => $selectedType,
                'civilization_id' => $request->civilization_id,
                'region_id' => $request->region_id,
                'min_rating' => $request->min_rating,
                'sort' => $request->sort,
            ],
        ]);
    }



    private function resolveMapMarkerType(Attraction $attraction): string
    {
        return match ($attraction->type) {
            Attraction::TYPE_BEACH, Attraction::TYPE_COASTAL => 'natural',
            Attraction::TYPE_ACTIVITY => 'museum',
            Attraction::TYPE_HISTORICAL => 'historical',
            default => 'historical',
        };
    }
}