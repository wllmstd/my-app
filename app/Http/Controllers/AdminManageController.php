<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class AdminManageController extends Controller
{
    public function index()
    {
        $users = User::all(); // Fetch all users from the database
        return view('admin.adminmanage', compact('users'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
    
        $user->delete();
    
        return redirect()->route('adminmanage')->with('success', 'User deleted successfully.');
    }
    
    public function edit($id)
{
    // Find the user by ID
    $user = User::findOrFail($id);
    
    // Return the edit form with the user data
    return view('admin.edit', compact('user'));
}

public function saveEdited(Request $request, $id)
{
    // Validate the incoming request
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'department' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:8',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Find the user by ID
    $user = User::findOrFail($id);

    // Update the user fields
    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->department = $request->department;
    $user->email = $request->email;

    // Update password if provided
    if ($request->password) {
        $user->password = bcrypt($request->password);
    }

    // Handle image upload if provided
    if ($request->hasFile('image')) {
        // Delete the old image if exists
        if ($user->image) {
            Storage::delete('public/' . $user->image);
        }

        // Store the new image and update the path
        $imagePath = $request->file('image')->store('profile_images', 'public');
        $user->image = $imagePath;
    }

    // Save the updated user
    $user->save();

    return redirect()->route('adminmanage')->with('success', 'User edited successfully!');
}



    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', // Ensure email uniqueness
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
        ]);

        // Create new user
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->department = $request->input('department');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password')); // Hash the password

        // Handle profile image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profile_images', 'public'); // Store the image
            $user->image = $imagePath;
        }

        // Save the new user
        $user->save();

        // Redirect with success message
        return redirect()->route('adminmanage')->with('success', 'User added successfully!');
    }
}
