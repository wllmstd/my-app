<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Hash;

class SupportManageController extends Controller
{
    public function index()
    {
        // Fetch all requests (profiles) from the database
        $profiles = UserRequest::all(); // You can add additional conditions if needed
        return view('support.supportmanage', compact('profiles'));
    }
}
