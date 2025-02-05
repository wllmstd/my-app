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
        // Validate login credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt authentication
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->department === 'Admin') {
                return redirect()->route('admindashboard');
            } elseif ($user->department === 'Profiler') {
                return redirect()->route('supportdashboard'); 
            } elseif ($user->department === 'Talent Acquisition') {
                return redirect()->route('userdashboard'); 
            }
        
            return redirect('/')->with('error', 'Unauthorized Access');
        }
        

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }



}


//Updated
