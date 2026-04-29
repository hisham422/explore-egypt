<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\ImageManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public function edit(): View
    {
        return view('admin.appearance.edit', [
            'heroImage' => SiteSetting::getValue('home_hero_image'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_image' => ['nullable'],
        ]);

        $currentPath = SiteSetting::getValue('home_hero_image');

        if ($request->hasFile('hero_image')) {
            $newPath = ImageManager::store($request->file('hero_image'), 'images/hero', 'home-hero');
            ImageManager::delete($currentPath);
            SiteSetting::setValue('home_hero_image', $newPath);
        }

        return redirect()
            ->route('admin.appearance.edit')
            ->with('status', 'Homepage hero image updated successfully.');
    }
}
