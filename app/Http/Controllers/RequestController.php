<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\UserRequest;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class RequestController extends Controller
{
    public function index()
    {
        $requests = UserRequest::all(); // Use UserRequest instead of RequestModel
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


}
