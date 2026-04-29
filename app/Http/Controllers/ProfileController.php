<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\ImageManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
{
    $favorites = $request->user()
        ->favorites()
        ->with(['attraction.civilization', 'attraction.region'])
        ->latest()
        ->get();

    return view('profile.edit', [
        'user' => $request->user(),
        'favorites' => $favorites,
    ]);
}

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $user->fill(Arr::except($validated, ['avatar', 'remove_avatar']));

        if ($request->boolean('remove_avatar') && filled($user->avatar)) {
            ImageManager::delete($user->avatar);
            $user->avatar = null;
        }

        if ($request->hasFile('avatar')) {
            ImageManager::delete($user->avatar);

            $user->avatar = ImageManager::store(
                $request->file('avatar'),
                'images/avatars',
                $user->name.' avatar'
            );
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        ImageManager::delete($user->avatar);

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
