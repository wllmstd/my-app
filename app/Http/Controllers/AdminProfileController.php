<?php

namespace App\Http\Controllers;
use Intervention\Image\Facades\Image; // Add this at the top
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


class AdminProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Get logged-in admin
        return view('admin.adminprofile', compact('user'));
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate input fields
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed', // ✅ Ensure password is confirmed
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && Storage::exists('public/' . $user->image)) {
                Storage::delete('public/' . $user->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('profile_images', 'public');
            $user->image = $imagePath; // Save the correct path in DB
        }

        // Update user details
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->department = $request->department;

        // ✅ Update password if provided
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function getProfileImage(Request $request)
{
    $user = auth()->user(); // Get logged-in user
    $imagePath = $user->image ? "profile_images/{$user->image}" : "profile_images/default.png";

    if (Storage::disk('public')->exists($imagePath)) {
        return response()->file(storage_path("app/public/{$imagePath}"));
    } else {
        abort(404); // Return 404 if the image is not found
    }
}

}