<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Civilization;
use App\Models\Region;
use App\Models\Review;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'civilizations' => Civilization::count(),
                'regions' => Region::count(),
                'attractions' => Attraction::count(),
                'users' => User::count(),
                'reviews' => Review::count(),
            ],
            'recentReviews' => Review::with(['user', 'attraction'])
                ->latest()
                ->limit(8)
                ->get(),
            'recentUsers' => User::latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
