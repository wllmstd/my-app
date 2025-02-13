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

        // Delete attached files
        if ($request->Attachment) {
            $files = json_decode($request->Attachment, true);
            foreach ($files as $file) {
                Storage::disk('public')->delete('attachments/' . $file);
            }
        }

        // Delete request record
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
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'format' => 'required|string|in:Geco Standard,Geco New Date,Geco New Rate,Blind,HTD,SAP,PCX, Accenture',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $userRequest = UserRequest::findOrFail($id);
        $userRequest->First_Name = $request->first_name;
        $userRequest->Last_Name = $request->last_name;
        $userRequest->Nationality = $request->nationality;
        $userRequest->Location = $request->location;
        $userRequest->Format = $request->format;

        $uploadedFiles = json_decode($userRequest->Attachment, true) ?? [];

        if ($request->hasFile('attachments')) {
            foreach ($uploadedFiles as $file) {
                Storage::delete('public/attachments/' . $file);
            }

            $uploadedFiles = [];
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('attachments', $filename, 'public');
                $uploadedFiles[] = $filename;
            }
        }

        $userRequest->Attachment = json_encode($uploadedFiles);
        $userRequest->Updated_Time = Carbon::now('Asia/Manila'); // Ensure correct timezone
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
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
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

}
