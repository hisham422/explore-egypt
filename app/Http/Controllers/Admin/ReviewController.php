<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Review;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function index(): View
    {
        $search = request('q');

        $reviews = Review::with(['user', 'attraction'])
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('attraction', function ($attractionQuery) use ($search) {
                        $attractionQuery->where('name', 'like', "%{$search}%");
                    })->orWhere('comment', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'search'));
    }

    public function create(): View
    {
        return view('admin.reviews.create', [
            'review' => new Review(),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'attractions' => Attraction::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        Review::create($data);

        return redirect()
            ->route('admin.reviews.index')
            ->with('status', 'Review created successfully.');
    }

    public function edit(Review $review): View
    {
        return view('admin.reviews.edit', [
            'review' => $review,
            'users' => User::orderBy('name')->get(['id', 'name']),
            'attractions' => Attraction::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $data = $this->validatedData($request, $review);

        $review->update($data);

        return redirect()
            ->route('admin.reviews.index')
            ->with('status', 'Review updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('status', 'Review deleted successfully.');
    }

    private function validatedData(Request $request, ?Review $review = null): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'attraction_id' => [
                'required',
                'exists:attractions,id',
                Rule::unique('reviews', 'attraction_id')
                    ->where(fn ($query) => $query->where('user_id', $request->integer('user_id')))
                    ->ignore($review?->id),
            ],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ]);
    }
}
