<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\UserRequest;

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
        // Find the request by ID
        $request = UserRequest::findOrFail($id);

        // Delete the attached file if exists
        if ($request->Attachment) {
            Storage::disk('public')->delete('attachments/' . $request->Attachment);
        }

        // Delete the request record
        $request->delete();

        return redirect()->route('requests.index')->with('success', 'Request deleted successfully.');
    }

    public function edit($id)
    {
        // Find the request by ID
        $request = UserRequest::findOrFail($id);

        // Return the edit form with the request data
        return view('user.edit_request', compact('request'));
    }

    public function saveEdited(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'format' => 'required|string|in:Geco Standard,Geco New Date,Geco New Rate,Blind,HTD,STD,PCX',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Find the request by ID
        $userRequest = UserRequest::findOrFail($id);

        // Update fields
        $userRequest->First_Name = $request->first_name;
        $userRequest->Last_Name = $request->last_name;
        $userRequest->Nationality = $request->nationality;
        $userRequest->Location = $request->location;
        $userRequest->Format = $request->format;

        // Handle file upload if provided
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($userRequest->Attachment) {
                Storage::delete('public/attachments/' . $userRequest->Attachment);
            }

            // Store new file and update path
            $filename = time() . '.' . $request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->storeAs('attachments', $filename, 'public');
            $userRequest->Attachment = $filename;
        }

        // Update timestamps
        $userRequest->Updated_Time = now();
        $userRequest->save();

        return redirect()->route('requests.index')->with('success', 'Request updated successfully.');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'format' => 'required|string|in:Geco Standard,Geco New Date,Geco New Rate,Blind,HTD,STD,PCX',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Handle file upload if provided
        $filename = null;
        if ($request->hasFile('attachment')) {
            $filename = time() . '.' . $request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->storeAs('attachments', $filename, 'public');
        }

        // Create new request
        UserRequest::create([
            'First_Name' => $request->first_name,
            'Last_Name' => $request->last_name,
            'Nationality' => $request->nationality,
            'Location' => $request->location,
            'Format' => $request->format,
            'Attachment' => $filename,
            'Status' => 'Pending', // Default status
            'Date_Created' => now(),
            'Updated_Time' => now(),
            'Users_ID' => Auth::id(), // Ensure Users_ID is provided
        ]);

        return redirect()->route('requests.index')->with('success', 'Request added successfully.');
    }

}
