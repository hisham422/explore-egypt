<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Civilization;
use App\Support\ImageManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CivilizationController extends Controller
{
    public function index(): View
    {
        $search = request('q');

        $civilizations = Civilization::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.civilizations.index', compact('civilizations', 'search'));
    }

    public function create(): View
    {
        return view('admin.civilizations.create', [
            'civilization' => new Civilization(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['image'] = $request->hasFile('image')
            ? ImageManager::store($request->file('image'), 'images/civilizations', $data['name'])
            : null;

        $data['hero_video_url'] = $this->storeHeroVideo($request, $data['name']);

        Civilization::create($data);

        return redirect()
            ->route('admin.civilizations.index')
            ->with('status', 'Civilization created successfully.');
    }

    public function edit(Civilization $civilization): View
    {
        return view('admin.civilizations.edit', compact('civilization'));
    }

    public function update(Request $request, Civilization $civilization): RedirectResponse
    {
        $data = $this->validatedData($request, $civilization);

        $data['image'] = $request->hasFile('image')
            ? tap(ImageManager::store($request->file('image'), 'images/civilizations', $data['name']), function () use ($civilization) {
                ImageManager::delete($civilization->image);
            })
            : $civilization->image;

        $data['hero_video_url'] = $this->storeHeroVideo($request, $data['name'], $civilization->hero_video_url);

        $civilization->update($data);

        return redirect()
            ->route('admin.civilizations.index')
            ->with('status', 'Civilization updated successfully.');
    }

    public function destroy(Civilization $civilization): RedirectResponse
    {
        ImageManager::delete($civilization->image);
        $civilization->delete();

        return redirect()
            ->route('admin.civilizations.index')
            ->with('status', 'Civilization deleted successfully.');
    }

    private function validatedData(Request $request, ?Civilization $civilization = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('civilizations', 'name')->ignore($civilization?->id),
            ],
            'description' => ['required', 'string'],
            'image' => ['nullable'],
            'hero_video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime', 'max:51200'],
            'hero_video_url' => ['nullable', 'string', 'max:2048'],
            'hero_video_remove' => ['nullable', 'boolean'],
        ]);
    }

    private function storeHeroVideo(Request $request, string $baseName, ?string $currentPath = null): ?string
    {
        if ($request->boolean('hero_video_remove')) {
            ImageManager::delete($currentPath);

            return null;
        }

        if ($request->hasFile('hero_video_file')) {
            ImageManager::delete($currentPath);

            return ImageManager::store($request->file('hero_video_file'), 'videos/civilizations', $baseName);
        }

        $heroVideoUrl = trim((string) $request->input('hero_video_url', ''));

        if ($heroVideoUrl !== '') {
            if ($currentPath && $currentPath !== $heroVideoUrl) {
                ImageManager::delete($currentPath);
            }

            $normalizedPath = ImageManager::normalizePath($heroVideoUrl);

            return Str::startsWith($heroVideoUrl, ['http://', 'https://'])
                ? $heroVideoUrl
                : $normalizedPath;
        }

        return $currentPath;
    }
}
