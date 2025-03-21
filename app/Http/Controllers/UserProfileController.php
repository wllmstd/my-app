<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserProfileController extends Controller
{
    // Show Profile Page
    public function index()
    {
        $user = Auth::user(); // Get the currently logged-in user
        return view('user.userprofile', compact('user'));
    }

    // Update Profile Information
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:25600', // Allows up to 10MB
            'password' => 'nullable|string|min:8|confirmed', // ✅ Ensure password confirmation
        ]);

        // Handle profile image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($user->image && Storage::exists('public/' . $user->image)) {
                Storage::delete('public/' . $user->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('profile_images', 'public');
            $user->image = $imagePath;
        }

        // Update other profile details
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;

        // ✅ Update password only if provided
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user.userprofile')->with('success', 'Profile updated successfully.');
    }

}