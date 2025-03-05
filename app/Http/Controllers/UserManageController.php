<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest; 

class UserManageController extends Controller
{
public function index()
{
    $userId = auth()->id(); // Get the logged-in user's ID

    $requests = UserRequest::where('Users_ID', $userId) // Only fetch requests created by this user
        ->orWhere('Status', 'Pending') // Also show all "Pending" requests globally
        ->get();

    return view('user.usermanage', compact('requests'));
}


}

