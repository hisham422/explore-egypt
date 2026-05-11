<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\AttractionImage;
use App\Models\Civilization;
use App\Models\CivilizationPeriod;
use App\Models\Region;
use App\Support\ImageManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttractionController extends Controller
{
    public function index(): View
    {
        $search = request('q');

        $attractions = Attraction::with(['civilization', 'region'])
            ->withCount('images')
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.attractions.index', compact('attractions', 'search'));
    }

    public function create(): View
    {
        return view('admin.attractions.create', [
            'attraction' => new Attraction(),
            'civilizations' => Civilization::orderBy('name')->get(['id', 'name']),
            'periods' => CivilizationPeriod::with('civilization')->orderBy('civilization_id')->orderBy('sort_order')->get(),
            'regions' => Region::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validatedData($request);

        $data['image'] = $request->hasFile('image')
            ? ImageManager::store($request->file('image'), 'images/attractions', $data['name'])
            : null;

        $attraction = Attraction::create($data);

        $this->storeGalleryImages($request, $attraction, $data['name']);

        return $this->savedResponse($request, 'Attraction created successfully.');
    }

    public function edit(Attraction $attraction): View
    {
        $attraction->load('images');

        return view('admin.attractions.edit', [
            'attraction' => $attraction,
            'civilizations' => Civilization::orderBy('name')->get(['id', 'name']),
            'periods' => CivilizationPeriod::with('civilization')->orderBy('civilization_id')->orderBy('sort_order')->get(),
            'regions' => Region::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Attraction $attraction): RedirectResponse|JsonResponse
    {
        $data = $this->validatedData($request);

        $data['image'] = $request->hasFile('image')
            ? tap(ImageManager::store($request->file('image'), 'images/attractions', $data['name']), function () use ($attraction) {
                ImageManager::delete($attraction->image);
            })
            : $attraction->image;

        $attraction->update($data);

        $this->storeGalleryImages($request, $attraction, $data['name']);

        return $this->savedResponse($request, 'Attraction updated successfully.');
    }

    public function destroy(Attraction $attraction): RedirectResponse
    {
        foreach ($attraction->images as $galleryImage) {
            ImageManager::delete($galleryImage->image);
        }

        ImageManager::delete($attraction->image);
        $attraction->delete();

        return redirect()
            ->route('admin.attractions.index')
            ->with('status', 'Attraction deleted successfully.');
    }

    public function destroyImage(Request $request, Attraction $attraction, AttractionImage $attractionImage): RedirectResponse|JsonResponse
    {
        abort_unless($attractionImage->attraction_id === $attraction->id, 404);

        ImageManager::delete($attractionImage->image);
        $attractionImage->delete();

        $this->reindexGalleryImages($attraction);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Gallery image deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.attractions.edit', $attraction)
            ->with('status', 'Gallery image deleted successfully.');
    }

    public function destroyMainImage(Request $request, Attraction $attraction): RedirectResponse|JsonResponse
    {
        if ($attraction->image) {
            ImageManager::delete($attraction->image);
            $attraction->update(['image' => null]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Main image deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.attractions.edit', $attraction)
            ->with('status', 'Main image deleted successfully.');
    }

    public function moveImage(Request $request, Attraction $attraction, AttractionImage $attractionImage): RedirectResponse
    {
        abort_unless($attractionImage->attraction_id === $attraction->id, 404);

        $direction = $request->string('direction')->toString();

        abort_unless(in_array($direction, ['up', 'down'], true), 422);

        $targetImage = $direction === 'up'
            ? $attraction->images()->where('sort_order', '<', $attractionImage->sort_order)->orderByDesc('sort_order')->orderByDesc('id')->first()
            : $attraction->images()->where('sort_order', '>', $attractionImage->sort_order)->orderBy('sort_order')->orderBy('id')->first();

        if ($targetImage) {
            DB::transaction(function () use ($attractionImage, $targetImage): void {
                $currentOrder = $attractionImage->sort_order;
                $attractionImage->update(['sort_order' => $targetImage->sort_order]);
                $targetImage->update(['sort_order' => $currentOrder]);
            });
        }

        return redirect()
            ->route('admin.attractions.edit', $attraction)
            ->with('status', 'Gallery order updated successfully.');
    }

    public function reorderImages(Request $request, Attraction $attraction): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'image_ids' => ['required', 'array', 'min:1'],
            'image_ids.*' => ['integer'],
        ]);

        $orderedIds = array_map('intval', $data['image_ids']);

        $galleryImages = $attraction->images()
            ->whereIn('id', $orderedIds)
            ->get()
            ->keyBy('id');

        abort_unless($galleryImages->count() === count($orderedIds), 404);

        DB::transaction(function () use ($orderedIds, $galleryImages): void {
            foreach ($orderedIds as $index => $imageId) {
                $galleryImages[$imageId]->update(['sort_order' => $index]);
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Gallery order saved successfully.',
            ]);
        }

        return redirect()
            ->route('admin.attractions.edit', $attraction)
            ->with('status', 'Gallery order saved successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', Rule::in(Attraction::TYPES)],
            'image' => ['nullable'],
            // accept both names to be tolerant of different forms
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:10240'],
            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:10240'],
            'location' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'civilization_id' => ['nullable', 'exists:civilizations,id'],
            'civilization_period_id' => [
                'nullable',
                Rule::exists('civilization_periods', 'id')->where(function ($query) use ($request) {
                    $civilizationId = $request->integer('civilization_id');

                    if ($civilizationId > 0) {
                        $query->where('civilization_id', $civilizationId);
                    }
                }),
            ],
            'region_id' => ['required', 'exists:regions,id'],
        ]);
    }

    private function storeGalleryImages(Request $request, Attraction $attraction, string $baseName): void
    {
        $files = $request->file('images') ?: $request->file('media');

        if (! $files) {
            return;
        }

        $nextOrder = (int) ($attraction->images()->max('sort_order') ?? -1) + 1;

        foreach ($files as $index => $file) {
            $path = ImageManager::store($file, 'images/attractions', $baseName.'-gallery-'.($index + 1));
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
            $type = $extension === 'mp4' ? 'video' : 'image';

            $attraction->images()->create([
                'image' => $path,
                'type' => $type,
                'sort_order' => $nextOrder++,
            ]);
        }
    }

    private function detectMediaType($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return $extension === 'mp4' ? 'video' : 'image';
    }

    private function reindexGalleryImages(Attraction $attraction): void
    {
        $attraction->load('images');

        foreach ($attraction->images->values() as $index => $galleryImage) {
            $galleryImage->update(['sort_order' => $index]);
        }
    }

    private function savedResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('admin.attractions.index'),
            ]);
        }

        return redirect()
            ->route('admin.attractions.index')
            ->with('status', $message);
    }
}
