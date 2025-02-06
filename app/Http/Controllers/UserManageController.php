<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest; 

class UserManageController extends Controller
{
    public function index()
    {
        $requests = UserRequest::all();
        return view('user.usermanage', compact('requests'));

    }

}

