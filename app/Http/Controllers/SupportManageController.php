<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Mail\RequestAcceptedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Mail\FileUploadedMail;
use Carbon\Carbon;


class SupportManageController extends Controller
{

    
    public function index()
    {
        $userId = Auth::id();
    
        // Get requests accepted by the logged-in profiler (excluding old completed ones for "all")
        $myAcceptedRequests = UserRequest::where('Accepted_By', $userId)
            ->where(function ($query) {
                $query->where('Status', '!=', 'Completed')
                      ->orWhere(function ($query) {
                          $query->where('Status', 'Completed')
                                ->whereDate('Updated_Time', now()->toDateString()); // Only today's completed
                      });
            })
            ->orderBy('Updated_Time', 'asc')
            ->get();
    
        // Get all requests that are still pending
        $profiles = UserRequest::whereNull('Accepted_By')
            ->orWhere('Status', 'Pending')
            ->get();
    
        // ✅ Get all completed requests for Completed Table
        $completedRequests = UserRequest::where('Status', 'Completed')
            ->orderBy('Updated_Time', 'desc')
            ->get();
    
        return view('support.supportmanage', compact('myAcceptedRequests', 'profiles', 'completedRequests'));
    }
    


    public function acceptRequest($id)
{
    Log::info("Received request for ID: " . $id);

    // Find the request
    $request = UserRequest::where('Request_ID', $id)->first();

    if (!$request) {
        Log::error("Request not found for ID: " . $id);
        return response()->json(['error' => 'Request not found'], 404);
    }

    // Log request details
    Log::info("Request found: " . json_encode($request));

    // Get the TA (User who made the request)
    $user = $request->creator; // ✅ Get the TA (request creator)

    // Check if the relationship is working
    if (!$user) {
        Log::error("Creator (TA) not found for request ID: " . $id);
    } else {
        Log::info("TA Found: " . $user->first_name . " " . $user->last_name . " | Email: " . $user->email);
    }

    // Get the Profiler (Authenticated User)
    $profiler = Auth::user(); // ✅ Profiler who accepted the request

    Log::info("Request accepted by: " . $profiler->first_name . " " . $profiler->last_name . " | Email: " . $profiler->email);

    // Update status
    $updated = UserRequest::where('Request_ID', $id)->update([
        'Status' => 'In Progress',
        'Accepted_By' => $profiler->id, 
        'Accepted_At' => now(), // ✅ Capture accepted timestamp

    ]);

    if ($updated) {
        Log::info("Status updated to 'In Progress' successfully!");

        // Send email notification
        if ($user && $user->email) {
            Log::info("Preparing to send email to: " . $user->email);
            Mail::send(new RequestAcceptedMail($user, $request, $profiler));
            Log::info("✅ Email successfully sent to: " . $user->email);
        } else {
            Log::error("🚨 User email not found. Email NOT sent.");
        }
    } else {
        Log::error("❌ Database update failed.");
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
            $uploadedFiles[] = $filename;
        }
    }

    // Merge old and new files
    $allFiles = array_merge($existingFiles, $uploadedFiles);

    // Save to database
    $userRequest->uploaded_format = json_encode($allFiles);
    $userRequest->save();

    // Send email notification
    $user = $userRequest->creator;  // Requester (TA)
    $profiler = Auth::user(); // Authenticated support user uploading the file

    if ($user && $user->email) {
        Log::info("Sending email to: " . $user->email);
        Mail::to($user->email)->send(new FileUploadedMail($user, $userRequest, $profiler, $uploadedFiles));
        Log::info("✅ Email successfully sent to: " . $user->email);
    } else {
        Log::error("🚨 User email not found. Email NOT sent.");
    }

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
public function checkFiles($id)
{
    $userRequest = UserRequest::findOrFail($id);
    $files = json_decode($userRequest->uploaded_format, true) ?? [];

    return response()->json(['fileCount' => count($files)]);
}




    
}