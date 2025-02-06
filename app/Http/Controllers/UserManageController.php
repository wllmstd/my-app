<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as RequestModel; 

class UserManageController extends Controller
{
    public function index()
    {
        $requests = RequestModel::all();
        return view('user.usermanage', compact('requests'));

    }

}

