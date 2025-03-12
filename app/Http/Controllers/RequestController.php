<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\UserRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestCompletedMail;
use App\Models\User;
use App\Mail\RequestRevisionMail;


class RequestController extends Controller
{
    public function index()
    {
        $userId = auth::id(); // Get the logged-in user's ID

        $requests = UserRequest::where('Users_ID', $userId) // Get only requests from the logged-in user
            ->get();

        return view('user.usermanage', compact('requests'));
    }


    public function destroy($id)
    {
        $request = UserRequest::findOrFail($id);

        // Delete attached files if any
        if (!empty($request->Attachment)) {
            $files = json_decode($request->Attachment, true);

            if (is_array($files)) {
                foreach ($files as $file) {
                    Storage::disk('public')->delete('attachments/' . $file);
                }
            }
        }

        // Delete request record
        $request->delete();

        return response()->json(['success' => 'Request deleted successfully']);
    }

    public function edit($id)
    {
        $userRequest = UserRequest::findOrFail($id);
        return view('user.edit_request', compact('userRequest')); // Pass it to the view
    }


    public function saveEdited(Request $request, $id)
    {
        // Validate input fields and files
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'format' => 'required|string|in:Geco Standard,Geco New Date,Geco New Rate,Blind,HTD,SAP,PCX,Accenture',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx|max:1024000',
        ]);

        // Find the request record
        $userRequest = UserRequest::findOrFail($id);
        $userRequest->First_Name = $request->first_name;
        $userRequest->Last_Name = $request->last_name;
        $userRequest->Nationality = $request->nationality;
        $userRequest->Location = $request->location;
        $userRequest->Format = $request->format;

        // Decode existing attachments or set an empty array
        $existingAttachments = json_decode($userRequest->Attachment, true) ?? [];

        // Handle new attachments (add without deleting old ones)
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('attachments', $filename, 'public');
                $existingAttachments[] = $filename; // Add new files to the existing list
            }
        }

        // Update the attachment field (keeping old and new files)
        $userRequest->Attachment = json_encode($existingAttachments);

        // Update timestamp
        $userRequest->Updated_Time = Carbon::now('Asia/Manila');

        // Save the updated record
        $userRequest->save();

        return redirect()->route('requests.index')->with('success', 'Request updated successfully.');
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'format' => 'required|string|in:Geco Standard,Geco New Date,Geco New Rate,Blind,HTD,SAP,PCX,Accenture',
            'uploaded_format' => 'nullable|file|mimes:pdf,doc,docx|max:1024000', // ✅ Validate uploaded_format as a file
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx|max:1024000',
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('attachments', $filename, 'public');
                $uploadedFiles[] = $filename;
            }
        }

        UserRequest::create([
            'First_Name' => $request->first_name,
            'Last_Name' => $request->last_name,
            'Nationality' => $request->nationality,
            'Location' => $request->location,
            'Format' => $request->format,
            'Attachment' => json_encode($uploadedFiles),
            'Status' => 'Pending',
            'Date_Created' => Carbon::now('Asia/Manila'), // Set timezone explicitly
            'Updated_Time' => Carbon::now('Asia/Manila'),
            'Users_ID' => Auth::id(),
        ]);

        return redirect()->route('requests.index')->with('success', 'Request added successfully.');
    }

    //Deletes attachments from the request
    public function deleteAttachment(Request $request, $id)
    {
        $userRequest = UserRequest::findOrFail($id);
        $existingAttachments = json_decode($userRequest->Attachment, true) ?? [];

        if (($key = array_search($request->file_name, $existingAttachments)) !== false) {
            // Delete file from storage
            Storage::disk('public')->delete('attachments/' . $request->file_name);

            // Remove file from array
            unset($existingAttachments[$key]);

            // Save updated attachment list
            $userRequest->Attachment = json_encode(array_values($existingAttachments));
            $userRequest->save();

            return response()->json(['success' => 'Attachment deleted successfully']);
        }

        return response()->json(['error' => 'File not found'], 404);
    }


    public function markAsDone(Request $request, $id)
    {
        $userRequest = UserRequest::findOrFail($id);
        $userRequest->Status = "Completed";
        $userRequest->Feedback = $request->feedback; // Store user feedback
        $userRequest->save();

        return response()->json(['success' => true]);
    }

    public function requestRevision($id, Request $request)
    {
        try {
            Log::info("Received revision request for ID: " . $id);

            if (!$id) {
                Log::error("Invalid request ID.");
                return response()->json(['success' => false, 'message' => 'Invalid request ID.'], 400);
            }

            // ✅ Ensure we fetch only one request (not a collection)
            $userRequest = UserRequest::where('Request_ID', $id)->firstOrFail();

            if (!$userRequest) {
                Log::error("❌ Request not found for ID: " . $id);
                return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
            }

            Log::info("User Request Data: " . json_encode($userRequest));

            // ✅ Ensure the profiler exists
            $profiler = User::where('id', $userRequest->accepted_by)->first();
            if (!$profiler) {
                Log::error("❌ Profiler not found for request ID: " . $id);
                return response()->json(['success' => false, 'message' => 'Profiler not found.'], 404);
            }

            // ✅ Get feedback and log it
            $feedback = $request->input('feedback');
            Log::info("Received Feedback: " . $feedback);

            // ✅ Update request status and feedback
            // ✅ Force update the feedback in the database
            $userRequest->feedback = $feedback;
            $userRequest->Status = 'Needs Revision';
            $userRequest->Updated_Time = now();
            $userRequest->save();

            Log::info("✅ Feedback successfully saved: " . $userRequest->feedback);


            Log::info("✅ Request updated successfully. Preparing to send email...");

            // ✅ Check before sending email
            if (!$userRequest) {
                Log::error("🚨 UserRequest is NULL. Email NOT sent.");
                return response()->json(['success' => false, 'message' => 'Failed to retrieve request.'], 500);
            }

            Mail::to($profiler->email)->send(new RequestRevisionMail($userRequest, $feedback));
            Log::info("✅ Email successfully sent to: " . $profiler->email);

            return response()->json(['success' => true, 'message' => 'Revision requested successfully, and email sent.']);
        } catch (\Exception $e) {
            Log::error("❌ Error updating request: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


    //Fetch the Profiler's Name 
    public function getRequestWithProfiler($id)
    {
        try {
            $request = DB::table('requests')
                ->leftJoin('users', 'users.id', '=', 'requests.accepted_by')
                ->where('requests.Request_ID', $id)
                ->select(
                    'requests.*',
                    'users.first_name AS profiler_first_name',
                    'users.last_name AS profiler_last_name'
                )
                ->first();

            if (!$request) {
                return response()->json(['message' => 'Request not found'], 404);
            }

            return response()->json($request);
        } catch (\Exception $e) {
            Log::error('Error fetching request details: ' . $e->getMessage());
            return response()->json(['message' => 'Server error occurred'], 500);
        }
    }

    public function markAsComplete($id, Request $request)
    {
        try {
            if (!$id) {
                return response()->json(['success' => false, 'message' => 'Invalid request ID.'], 400);
            }

            $userRequest = UserRequest::findOrFail($id);

            if (!$userRequest) {
                return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
            }

            // Log the request details
            Log::info("User Request Data: " . json_encode($userRequest));

            // ✅ Use the relationship to get the profiler
            $profiler = $userRequest->accepter;

            Log::info("Profiler Retrieved: " . json_encode($profiler));

            // Update the request status in the database
            DB::table('requests')
                ->where('Request_ID', $id)
                ->update([
                    'Status' => 'Completed',
                    'Updated_Time' => now(),
                    'feedback' => $request->input('feedback')
                ]);

            // Get the requester (TA)
            $user = $userRequest->creator;

            // Send email notification to the profiler
            if ($profiler && $profiler->email) {
                Mail::to($profiler->email)->send(new RequestCompletedMail($user, $userRequest, $profiler));
                Log::info("✅ Email successfully sent to: " . $profiler->email);
            } else {
                Log::error("🚨 Profiler email not found. Email NOT sent.");
            }

            return response()->json(['success' => true, 'message' => 'Request marked as complete, and email notification sent.']);
        } catch (\Exception $e) {
            Log::error("❌ Email sending failed: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }



    public function getRequestDetails($id)
    {
        try {
            $request = DB::table('requests')
                ->leftJoin('users', 'users.id', '=', 'requests.Users_ID') // ✅ Ensure correct user ID
                ->where('requests.Request_ID', $id)
                ->select(
                    'requests.*',
                    'users.first_name AS requested_by_first_name',
                    'users.last_name AS requested_by_last_name'
                )
                ->first();

            if (!$request) {
                return response()->json(['message' => 'Request not found'], 404);
            }

            return response()->json($request);
        } catch (\Exception $e) {
            Log::error('Error fetching request details: ' . $e->getMessage());
            return response()->json(['message' => 'Server error occurred'], 500);
        }
    }
}
