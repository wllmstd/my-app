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

    public function acceptRequest($id)
    {
        \Log::info("Received request for ID: " . $id);
    
        // Find the request
        $request = UserRequest::where('Request_ID', $id)->first();
    
        if (!$request) {
            \Log::error("Request not found for ID: " . $id);
            return response()->json(['error' => 'Request not found'], 404);
        }
    
        // Log old status
        \Log::info("Old Status: " . $request->Status);
    
        // Try updating using the update() method instead of save()
        $updated = UserRequest::where('Request_ID', $id)->update(['Status' => 'In Progress']);
    
        if ($updated) {
            \Log::info("Status updated to 'In Progress' successfully!");
        } else {
            \Log::error("Database update failed.");
        }
    
        return response()->json(['success' => 'Request accepted successfully', 'status' => 'In Progress']);
    }
    
    
}