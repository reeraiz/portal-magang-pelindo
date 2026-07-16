<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Division;
use App\Models\InternshipType;
use App\Models\EducationLevel;
use App\Models\University;
use App\Models\Gender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $mailFromName = \App\Models\Setting::where('key', 'mail_from_name')->value('value') ?? 'Pelindo';
        $mailFromAddress = \App\Models\Setting::where('key', 'mail_from_address')->value('value') ?? 'sarhamsan32@gmail.com';

        return view('profile.edit', [
            'user' => $request->user(),
            'divisions' => Division::orderBy('name')->get(),
            'internshipTypes' => InternshipType::orderBy('name')->get(),
            'educationLevels' => EducationLevel::orderBy('name')->get(),
            'universities' => University::orderBy('name')->get(),
            'genders' => Gender::orderBy('name')->get(),
            'mailFromName' => $mailFromName,
            'mailFromAddress' => $mailFromAddress,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

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

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
