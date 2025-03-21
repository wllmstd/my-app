<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class SupportProfileController extends Controller
{
    // Show Profile Page
    public function index()
    {
        $user = Auth::user(); // Get the currently logged-in user
        return view('support.supportprofile', compact('user'));
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
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', 
        ]);
    
        // Update profile image if uploaded
        if ($request->hasFile('image')) {
            if ($user->image && Storage::exists('public/' . $user->image)) {
                Storage::delete('public/' . $user->image);
            }
            $imagePath = $request->file('image')->store('profile_images', 'public');
            $user->image = $imagePath;
        }
    
        // ✅ Update user details
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
    
        // ✅ Only update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
    
        $user->save();
    
        return redirect()->route('support.supportprofile')->with('success', 'Profile updated successfully.');
    }
    
    

}