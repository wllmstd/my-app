<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


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
    
    public function uploadFormat(Request $request, $id)
    {
        $validated = $request->validate([
            'uploaded_format' => 'required|array',
            'uploaded_format.*' => 'file|mimes:pdf,doc,docx|max:10240', // Max 10MB per file
        ]);
    
        $userRequest = UserRequest::findOrFail($id);
    
        // Retrieve existing files if any
        $existingFiles = $userRequest->uploaded_format ? json_decode($userRequest->uploaded_format, true) : [];
    
        // Ensure $existingFiles is an array
        if (!is_array($existingFiles)) {
            $existingFiles = [];
        }
    
        // Handle multiple file uploads
        $uploadedFiles = [];
        if ($request->hasFile('uploaded_format')) {
            foreach ($request->file('uploaded_format') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('uploads', $filename, 'public');
                $uploadedFiles[] = $filename; // Add new file
            }
        }
    
        // Merge old and new files
        $allFiles = array_merge($existingFiles, $uploadedFiles);
    
        // Save to database
        $userRequest->uploaded_format = json_encode($allFiles);
        $userRequest->save();
    
        return response()->json([
            'success' => 'Files uploaded successfully!',
            'files' => $uploadedFiles
        ]);
    }

    public function deleteFile(Request $request, $id)
{
    $request->validate([
        'file_name' => 'required|string',
    ]);

    $userRequest = UserRequest::findOrFail($id);
    $existingFiles = json_decode($userRequest->uploaded_format, true) ?? [];

    // Remove file from array
    if (($key = array_search($request->file_name, $existingFiles)) !== false) {
        Storage::disk('public')->delete('uploads/' . $request->file_name);
        unset($existingFiles[$key]);
        $userRequest->uploaded_format = json_encode(array_values($existingFiles));
        $userRequest->save();

        return response()->json(['success' => 'File deleted successfully!']);
    }

    return response()->json(['error' => 'File not found'], 404);
}

public function forwardRequest($id)
{
    // Find the request by ID
    $request = UserRequest::findOrFail($id);

    if (!$request) {
        return response()->json(['error' => 'Request not found'], 404);
    }

    // Update the request status to "Under Review"
    $request->Status = 'Under Review';
    $request->save();

    return response()->json(['success' => 'Request forwarded successfully!']);
}




    
}