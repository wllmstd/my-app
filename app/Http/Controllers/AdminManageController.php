<?php

namespace App\Http\Controllers;

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
        $user = User::findOrFail($id);
        
        // Update first_name and last_name instead of name
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->department = $request->department;
        $user->email = $request->email;
        
        // Check if an image has been uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
    
            // Store the new image and update the user record with the image path
            $imagePath = $request->file('image')->store('profile_images', 'public');
            $user->image = $imagePath;
        }
        
        // Save the updated user data
        $user->save();
    
        return redirect()->route('adminmanage')->with('success', 'User updated successfully.');
    }
    

}
