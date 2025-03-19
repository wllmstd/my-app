<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\UserRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupportDashboardController extends Controller
{
    public function index()
    {
        return view('support.supportdashboard');
    }
    

    public function getPendingRequestsCount()
{
    $pendingRequestsCount = UserRequest::where('Status', 'Pending')->count();

    return response()->json([
        'pendingRequestsCount' => $pendingRequestsCount
    ]);
}


public function getRequestStatusCounts()
{
    try {
        $userId = auth()->id(); // or pass the ID dynamically if needed
        
        $statusCounts = UserRequest::select('Status', \DB::raw('COUNT(*) as count'))
            ->where('accepted_by', $userId) // Filter by accepted_by instead of Users_ID
            ->groupBy('Status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [strtolower(str_replace(' ', '_', $item->Status)) => $item->count];
            });

        $formattedCounts = [
            'in_progress'    => $statusCounts['in_progress'] ?? 0,
            'under_review'   => $statusCounts['under_review'] ?? 0,
            'needs_revision' => $statusCounts['needs_revision'] ?? 0,
            'completed'      => $statusCounts['completed'] ?? 0,
        ];

        return response()->json($formattedCounts);
    } catch (\Exception $e) {
        \Log::error('Error fetching support request status counts:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
}


public function getAcceptedRequestsByDay()
{
    try {
        $userId = auth()->id();

        $acceptedRequests = UserRequest::select(
                \DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                \DB::raw('COUNT(*) as count')
            )
            ->where('accepted_by', $userId)
            ->groupBy('day_of_week')
            ->get()
            ->mapWithKeys(function ($item) {
                // Map day_of_week (1 = Sunday) to Monday to Friday index
                $dayMap = [1 => 6, 2 => 0, 3 => 1, 4 => 2, 5 => 3, 6 => 4, 7 => 5];
                $index = $dayMap[$item->day_of_week] ?? null;
                
                if ($index !== null && $index < 5) {
                    return [$index => $item->count];
                }
            });

        // Fill missing days with 0
        $formattedCounts = [];
        for ($i = 0; $i < 5; $i++) {
            $formattedCounts[] = $acceptedRequests[$i] ?? 0;
        }

        return response()->json($formattedCounts);
    } catch (\Exception $e) {
        \Log::error('Error fetching accepted requests by day:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
}





    
}