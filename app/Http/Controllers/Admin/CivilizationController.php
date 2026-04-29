<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Civilization;
use App\Support\ImageManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);
    }
}
