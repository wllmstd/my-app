<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest; 
use Illuminate\Support\Facades\Auth;

class UserManageController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // Get the logged-in user's ID
    
        $requests = UserRequest::where('Users_ID', $userId) // Get only requests from the logged-in user
            ->get();
    
        return view('user.usermanage', compact('requests'));
    }
    

}

