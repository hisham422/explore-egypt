<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\ImageManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppearanceController extends Controller
{
    public function edit(): View
    {
        $heroVideo = SiteSetting::getValue('home_hero_video_url');
        $heroVideoUrl = filled($heroVideo)
            ? (Str::startsWith($heroVideo, ['http://', 'https://'])
                ? $heroVideo
                : asset('storage/'.$heroVideo))
            : null;

        return view('admin.appearance.edit', [
            'heroImage' => SiteSetting::getValue('home_hero_image'),
            'heroVideo' => $heroVideo,
            'heroVideoUrl' => $heroVideoUrl,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'hero_image' => ['nullable', 'image', 'max:10240'],
            'hero_video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime', 'max:51200'],
            'hero_video_url' => ['nullable', 'string', 'max:2048'],
            'hero_video_remove' => ['nullable', 'boolean'],
        ]);

        $currentImagePath = SiteSetting::getValue('home_hero_image');
        $currentVideoPath = SiteSetting::getValue('home_hero_video_url');

        if ($request->hasFile('hero_image')) {
            $newImagePath = ImageManager::store($request->file('hero_image'), 'images/hero', 'home-hero');
            ImageManager::delete($currentImagePath);
            SiteSetting::setValue('home_hero_image', $newImagePath);
        }

        $shouldRemoveVideo = $request->boolean('hero_video_remove');
        $enteredVideoUrl = trim((string) $request->input('hero_video_url', ''));

        if ($shouldRemoveVideo) {
            ImageManager::delete($currentVideoPath);
            $currentVideoPath = null;
            SiteSetting::setValue('home_hero_video_url', null);
        }

        if ($request->hasFile('hero_video_file')) {
            $newVideoPath = ImageManager::store($request->file('hero_video_file'), 'videos/hero', 'home-hero-video');
            ImageManager::delete($currentVideoPath);
            SiteSetting::setValue('home_hero_video_url', $newVideoPath);
        } elseif ($enteredVideoUrl !== '') {
            $normalizedVideoPath = Str::startsWith($enteredVideoUrl, ['http://', 'https://'])
                ? $enteredVideoUrl
                : ImageManager::normalizePath($enteredVideoUrl);

            if ($normalizedVideoPath !== $currentVideoPath) {
                ImageManager::delete($currentVideoPath);
                SiteSetting::setValue('home_hero_video_url', $normalizedVideoPath);
            }
        }

        return redirect()
            ->route('admin.appearance.edit')
            ->with('status', 'Homepage hero media updated successfully.');
    }
}
