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
        
        // ✅ Table 1: Non-completed requests + only today's completed requests
        $requests = UserRequest::where('Users_ID', $userId)
            ->where(function ($query) {
                $query->where('Status', '!=', 'Completed') // ✅ Include all non-completed requests
                    ->orWhere(function ($query) {
                        $query->where('Status', 'Completed') // ✅ Include today's completed requests
                            ->whereDate('Updated_Time', now()->toDateString());
                    });
            })
            ->get();    
    
        // ✅ Table 2: Fetch all completed requests (all-time)
        $completedRequests = UserRequest::where('Users_ID', $userId)
            ->where('Status', 'Completed')
            ->orderBy('Updated_Time', 'desc')
            ->get();
    
        // ✅ Ensure variable is always defined to prevent errors
        if ($completedRequests->isEmpty()) {
            $completedRequests = collect(); // Assign an empty collection instead of null
        }
    
        return view('user.usermanage', compact('requests', 'completedRequests'));
    }
    
    
}
