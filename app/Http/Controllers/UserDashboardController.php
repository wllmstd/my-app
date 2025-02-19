<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $totalRequests = UserRequest::where('Users_ID', Auth::id())->count();
    
        return view('user.userdashboard', compact('totalRequests'));
    }
    

    // AJAX function to get request counts for chart
    public function getRequestCounts()
    {
        $totalRequests = UserRequest::where('Users_ID', Auth::id())->count();
    
        return response()->json(['totalRequests' => $totalRequests]);
    }

    public function getRequestStatusCounts()
    {
        $statusCounts = UserRequest::selectRaw("Status, COUNT(*) as count")
            ->groupBy('Status')
            ->pluck('count', 'Status');
    
        return response()->json([
            'pending' => $statusCounts['Pending'] ?? 0,
            'in_progress' => $statusCounts['In Progress'] ?? 0,
            'completed' => $statusCounts['Completed'] ?? 0
        ]);
    }
    

    
}
