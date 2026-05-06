<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\CivilizationPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CivilizationPeriodController extends Controller
{
    public function index(): View
    {
        $search = request('q');

        $periods = CivilizationPeriod::query()
            ->with('civilization')
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('civilization', function ($civilizationQuery) use ($search) {
                        $civilizationQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('civilization_id')
            ->orderBy('sort_order')
            ->paginate(12)
            ->withQueryString();

        return view('admin.civilization-periods.index', compact('periods', 'search'));
    }

    public function create(): View
    {
        return view('admin.civilization-periods.create', [
            'period' => new CivilizationPeriod(),
            'civilizations' => Civilization::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        CivilizationPeriod::create($data);

        return redirect()
            ->route('admin.civilization-periods.index')
            ->with('status', 'Civilization period created successfully.');
    }

    public function edit(CivilizationPeriod $period): View
    {
        return view('admin.civilization-periods.edit', [
            'period' => $period,
            'civilizations' => Civilization::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, CivilizationPeriod $period): RedirectResponse
    {
        $data = $this->validatedData($request, $period);

        $period->update($data);

        return redirect()
            ->route('admin.civilization-periods.index')
            ->with('status', 'Civilization period updated successfully.');
    }

    public function destroy(CivilizationPeriod $period): RedirectResponse
    {
        $period->delete();

        return redirect()
            ->route('admin.civilization-periods.index')
            ->with('status', 'Civilization period deleted successfully.');
    }

    private function validatedData(Request $request, ?CivilizationPeriod $civilizationPeriod = null): array
    {
        return $request->validate([
            'civilization_id' => ['required', 'exists:civilizations,id'],
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('civilization_periods', 'title')
                    ->where('civilization_id', $request->integer('civilization_id'))
                    ->ignore($civilizationPeriod?->id),
            ],
            'start_year' => ['required', 'integer'],
            'end_year' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'rulers' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    public function attractions(CivilizationPeriod $period): View
    {
        $period->load('civilization');
        
        // Get attractions linked to this period
        $linkedAttractions = $period->attractions()
            ->orderBy('name')
            ->get();

        // Get all attractions from the same civilization that aren't linked yet
        $availableAttractions = Attraction::query()
            ->where('civilization_id', $period->civilization_id)
            ->whereNull('civilization_period_id')
            ->orWhere(function ($query) use ($period) {
                $query->where('civilization_id', $period->civilization_id)
                    ->where('civilization_period_id', $period->id);
            })
            ->orderBy('name')
            ->get()
            ->filter(function ($attraction) use ($linkedAttractions) {
                return !$linkedAttractions->contains($attraction->id);
            });

        return view('admin.civilization-periods.attractions', compact(
            'period',
            'linkedAttractions',
            'availableAttractions'
        ));
    }

    public function attachAttraction(Request $request, CivilizationPeriod $period): RedirectResponse
    {
        $validated = $request->validate([
            'attraction_id' => ['required', 'exists:attractions,id'],
        ]);

        $attraction = Attraction::findOrFail($validated['attraction_id']);

        // Verify attraction belongs to the same civilization
        if ($attraction->civilization_id !== $period->civilization_id) {
            return back()->with('error', 'This attraction does not belong to the same civilization.');
        }

        // Attach the attraction
        $attraction->update(['civilization_period_id' => $period->id]);

        return back()->with('status', 'Attraction added to this period successfully.');
    }

    public function detachAttraction(CivilizationPeriod $period, Attraction $attraction): RedirectResponse
    {
        // Verify attraction is linked to this period
        if ($attraction->civilization_period_id !== $period->id) {
            return back()->with('error', 'This attraction is not linked to this period.');
        }

        $attraction->update(['civilization_period_id' => null]);

        return back()->with('status', 'Attraction removed from this period successfully.');
    }
}