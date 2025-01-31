<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show the sign-up form
    public function showSignup()
    {
        return view('signup');  // This assumes you have a signup.blade.php file
    }

    // Handle the login request
    public function login(Request $request)
    {
        // Validate the request for email and password
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to log in the user with the provided credentials
        if (Auth::attempt($credentials)) {
            // Redirect to the dashboard page if login is successful
            return redirect()->intended('admindashboard');
        }

        // Return back with an error message if login fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    // Handle the sign-up request
    public function signup(Request $request)
    {
        \Log::info('Received signup data:', $request->all()); // Debugging: Log request data
    
        // Validate the input
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return redirect()->route('signup')
                             ->withErrors($validator)
                             ->withInput();
        }
    
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profile_images', 'public');
        }
    
        try {
            // Explicitly ensure all required fields are inserted
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'department' => $request->input('department'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'image' => $imagePath,
            ]);
    
            if ($user) {
                \Log::info('User successfully created:', $user->toArray());
                return redirect()->route('login')->with('success', 'Account created successfully. Please log in.');
            } else {
                throw new \Exception('Failed to create user.');
            }
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->route('signup')->with('error', 'Failed to create the account. Please try again.');
        }
    }
    


}
