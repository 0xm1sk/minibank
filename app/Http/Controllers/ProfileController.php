<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view("profile.edit", compact("user"));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                "unique:users,email," . $user->id,
            ],
            "phone" => ["nullable", "string", "max:20"],
            "address" => ["nullable", "string", "max:500"],
            "date_of_birth" => ["nullable", "date"],
        ]);

        $user->update($validated);

        return redirect()
            ->route("profile.edit")
            ->with("success", "Profile updated successfully.");
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            "password" => ["required", "current_password"],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/");
    }
}
