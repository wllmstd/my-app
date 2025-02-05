<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

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
}


//Updated
