<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportDashboardController extends Controller
{
    public function index()
    {
        return view('support.supportdashboard');
    }
}
