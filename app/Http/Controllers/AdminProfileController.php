<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    ]);

    // Handle image upload with cropping
    if ($request->hasFile('image')) {
        // Delete old image if it exists
        if ($user->image && Storage::exists('public/' . $user->image)) {
            Storage::delete('public/' . $user->image);
        }

        $imageFile = $request->file('image');
        $imageName = time() . '.' . $imageFile->getClientOriginalExtension();
        $imagePath = 'profile_images/' . $imageName;

        // Resize and crop image to a square (150x150) centered
        $image = \Intervention\Image\Facades\Image::make($imageFile)
            ->fit(150, 150, function ($constraint) {
                $constraint->upsize(); // Prevent stretching
            });

        // Save the image to storage
        Storage::put('public/' . $imagePath, (string) $image->encode());

        // Save path to the database
        $user->image = $imagePath;
    }

    // Update user details
    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->email = $request->email;
    $user->department = $request->department;
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