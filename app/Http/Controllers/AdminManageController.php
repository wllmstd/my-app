<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User; // Change this based on your table

class AdminManageController extends Controller
{
    public function index()
    {
        $users = User::all(); // Fetch all data from the table
        return view('admin.adminmanage', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }    


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('adminmanage')->with('success', 'User deleted successfully.');
    }
    
    public function update(Request $request, $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
    
        // Validate the input (optional but recommended)
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Update the user's details
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->department = $request->input('department');
        $user->email = $request->input('email');
    
        // If a new password is provided, hash and update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
    
        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($user->image) {
                Storage::disk('public')->delete($user->image); // Remove old image
            }
    
            // Store the new image
            $imagePath = $request->file('image')->store('profile_images', 'public');
            $user->image = $imagePath;
        }
    
        // Save the updated user
        $user->save();
    
        // Redirect with success message
        return redirect()->route('adminmanage')->with('success', 'User updated successfully.');
    }


    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', // Ensure the email is unique
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
        ]);

        // Create a new user record
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->department = $request->input('department');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password')); // Hash the password

        // If a profile image is uploaded, handle the image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profile_images', 'public'); // Store image in public disk
            $user->image = $imagePath;
        }

        // Save the new user
        $user->save();

        // Redirect back with a success message
        return redirect()->route('adminmanage')->with('success', 'User added successfully!');
    }


    

}
