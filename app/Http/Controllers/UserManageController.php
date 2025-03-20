<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserManageController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); 
        
        $requests = UserRequest::where('Users_ID', $userId)
        ->where(function ($query) {
            $query->where('Status', '!=', 'Completed') // ✅ Include all non-completed requests
                ->orWhere(function ($query) {
                    $query->where('Status', 'Completed') // ✅ Include completed requests, but only today's
                        ->whereDate('Updated_Time', now()->toDateString());
                });
        })
        ->get();    
    // Table 2: All completed requests (all time)
    $completedRequests = UserRequest::where('Users_ID', $userId)
        ->where('Status', 'Completed')
        ->get();
    
        return view('user.usermanage', compact('requests', 'completedRequests'));
    }
    
}
