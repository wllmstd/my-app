<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Hash;

class SupportManageController extends Controller
{
    public function index()
    {
        $userId = auth()->id(); // Get logged-in user's ID
    
        // Get requests accepted by the logged-in profiler
        $myAcceptedRequests = UserRequest::where('Accepted_By', $userId)
        ->orderBy('Updated_Time', 'asc') // Sort by latest accepted request
        ->get();
        // Get all requests that are still pending
        $profiles = UserRequest::whereNull('Accepted_By')->orWhere('Status', 'Pending')->get();
    
        return view('support.supportmanage', compact('myAcceptedRequests', 'profiles'));
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
        $updated = UserRequest::where('Request_ID', $id)->update([
            'Status' => 'In Progress',
            'Accepted_By' => auth()->id(), // Store the ID of the logged-in profiler
        ]);    
        if ($updated) {
            \Log::info("Status updated to 'In Progress' successfully!");
        } else {
            \Log::error("Database update failed.");
        }
    
        return response()->json(['success' => 'Request accepted successfully', 'status' => 'In Progress']);
    }
    
    public function uploadFiles(Request $request)
{
    $request->validate([
        'request_id' => 'required|exists:user_requests,Request_ID',
        'files.*' => 'file|max:2048', // Max file size 2MB
    ]);

    $userRequest = UserRequest::where('Request_ID', $request->request_id)->first();
    $existingAttachments = json_decode($userRequest->Attachment, true) ?? [];
    
    $newAttachments = [];
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $path = $file->store('attachments', 'public'); // Store in `storage/app/public/attachments`
            $newAttachments[] = $path;
        }
    }

    // Merge new files with existing ones
    $userRequest->Attachment = json_encode(array_merge($existingAttachments, $newAttachments));
    $userRequest->save();

    return response()->json(['message' => 'Files uploaded successfully!', 'files' => $newAttachments]);
}

    
}