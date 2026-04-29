<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AuditLog;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'student' => $request->user()->student,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $oldValues = [
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ];

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        AuditLog::recordIfAdmin(
            'admin_profile_updated',
            'user',
            $request->user()->id,
            $request->user()->email,
            'Updated system admin account profile information.',
            $oldValues,
            [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ]
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the authenticated student's personal details.
     */
    public function updateStudentProfile(Request $request): RedirectResponse
    {
        abort_if(! $request->user()->isStudent(), 403);

        /** @var Student|null $student */
        $student = $request->user()->student;

        abort_if($student === null, 404, 'Student profile not found.');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo_path && Storage::disk('public')->exists($student->photo_path)) {
                Storage::disk('public')->delete($student->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')->store('student-photos', 'public');
        }

        unset($validated['photo']);

        $student->update($validated);

        $request->user()->update([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
        ]);

        return Redirect::route('profile.edit')->with('status', 'student-profile-updated');
    }

    /**
     * Update only the authenticated student's profile photo.
     */
    public function updateStudentPhoto(Request $request): RedirectResponse
    {
        abort_if(! $request->user()->isStudent(), 403);

        /** @var Student|null $student */
        $student = $request->user()->student;
        abort_if($student === null, 404, 'Student profile not found.');

        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        if ($student->photo_path && Storage::disk('public')->exists($student->photo_path)) {
            Storage::disk('public')->delete($student->photo_path);
        }

        $student->update([
            'photo_path' => $validated['photo']->store('student-photos', 'public'),
        ]);

        return Redirect::route('profile.edit')->with('status', 'student-photo-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        abort_if($request->user()->isStudent(), 403, 'Students are not allowed to delete their accounts.');

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
