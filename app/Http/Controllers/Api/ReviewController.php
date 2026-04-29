<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReviewController extends Controller
{
    public function index($attractionId)
    {
        $reviews = Review::with('user')
            ->where('attraction_id', $attractionId)
            ->latest()
            ->get();

        return response()->json([
            'data' => $reviews
        ]);
    }

    public function store(StoreReviewRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $review = DB::transaction(function () use ($request) {
                return Review::updateOrCreate(
                    [
                        'user_id' => $request->user()->id,
                        'attraction_id' => $request->integer('attraction_id'),
                    ],
                    [
                        'rating' => $request->integer('rating'),
                        'comment' => $request->string('comment')->toString() ?: null,
                    ]
                );
            });

            $message = $review->wasRecentlyCreated
                ? 'Review created successfully'
                : 'Review updated successfully';

            if (! $request->expectsJson()) {
                return redirect()->back()->with('review_status', $message);
            }

            return response()->json([
                'message' => $message,
                'data' => $review->load('user'),
            ], $review->wasRecentlyCreated ? 201 : 200);

        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Server error',
                'errors' => ['server' => ['Something went wrong']]
            ], 500);
        }
    }
}