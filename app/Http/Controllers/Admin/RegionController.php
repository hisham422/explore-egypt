<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Support\ImageManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    public function index(): View
    {
        $search = request('q');

        $regions = Region::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.regions.index', compact('regions', 'search'));
    }

    public function create(): View
    {
        return view('admin.regions.create', [
            'region' => new Region(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $data['image'] = $request->hasFile('image')
            ? ImageManager::store($request->file('image'), 'images/regions', $data['name'])
            : null;

        Region::create($data);

        return redirect()
            ->route('admin.regions.index')
            ->with('status', 'Region created successfully.');
    }

    public function edit(Region $region): View
    {
        return view('admin.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region): RedirectResponse
    {
        $data = $this->validatedData($request, $region);

        $data['image'] = $request->hasFile('image')
            ? tap(ImageManager::store($request->file('image'), 'images/regions', $data['name']), function () use ($region) {
                ImageManager::delete($region->image);
            })
            : $region->image;

        $region->update($data);

        return redirect()
            ->route('admin.regions.index')
            ->with('status', 'Region updated successfully.');
    }

    public function destroy(Region $region): RedirectResponse
    {
        ImageManager::delete($region->image);
        $region->delete();

        return redirect()
            ->route('admin.regions.index')
            ->with('status', 'Region deleted successfully.');
    }

    private function validatedData(Request $request, ?Region $region = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions', 'name')->ignore($region?->id),
            ],
            'description' => ['nullable', 'string'],
            'image' => ['nullable'],
        ]);
    }
}
