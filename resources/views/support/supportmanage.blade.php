<!-- supportmanage.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manage Profiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" defer></script>


</head>

<body>

    <!-- Include the navbar here -->
    @include('support.support_navbar')
    <div class="scroll-container">

        <div class="container mt-4">
            <div class="filter-container">
                <div class="filter-container">
                    <!-- Toggle Button (Always Visible) -->
                    <button id="toggle-btn" class="btn btn-outline-secondary">☰ Filters</button>

                    <!-- Filter Buttons (Initially Hidden) -->
                    <div id="filter-buttons" class="filter-buttons">
                        <button class="btn btn-outline-dark filter-btn active" data-filter="all">
                            <div class="status-label-wrapper">
                                All Tables <span id="count-all" class="status-badge">0</span>
                            </div>
                        </button>
                        <button class="btn btn-outline-warning filter-btn" data-filter="Pending">
                            <span class="new-badge" id="new-badge-pending">NEW</span>
                            <div class="status-label-wrapper">
                                Pending Requests <span id="count-pending" class="status-badge">0</span>
                            </div>
                        </button>
                        <button class="btn btn-outline-primary filter-btn" data-filter="In Progress">
                            <span class="new-badge" id="new-badge-in-progress">NEW</span>
                            <div class="status-label-wrapper">
                                In Progress <span id="count-in-progress" class="status-badge">0</span>
                            </div>
                        </button>
                        <button class="btn btn-outline-orange filter-btn" data-filter="Under Review">
                            <span class="new-badge" id="new-badge-under-review">NEW</span>
                            <div class="status-label-wrapper">
                                Under Review <span id="count-under-review" class="status-badge">0</span>
                            </div>
                        </button>
                        <button class="btn btn-outline-danger filter-btn" data-filter="Needs Revision">
                            <span class="new-badge" id="new-badge-needs-revision">NEW</span>
                            <div class="status-label-wrapper">
                                Needs Revision <span id="count-needs-revision" class="status-badge">0</span>
                            </div>
                        </button>
                        <button class="btn btn-outline-success filter-btn" data-filter="Completed">
                            <span class="new-badge" id="new-badge-completed">NEW</span>
                            <div class="status-label-wrapper">
                                Completed <span id="count-completed" class="status-badge">0</span>
                            </div>
                        </button>
                    </div>
                </div>

            </div>




            <!-- Table 1: My Accepted Requests -->
            <table id="acceptedRequestsTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Status</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Nationality</th>
                        <th>Location</th>
                        <th>Format</th>
                        <th>Attachments</th>
                        <th>Updated Time</th>
                        <th>Submit</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($myAcceptedRequests as $index => $request)
                    <tr class="request-row" data-status="{{ $request->Status }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($request->Status === 'Pending')
                            <span class="badge bg-warning text-dark">{{ $request->Status }}</span> <!-- Yellow -->
                            @elseif($request->Status === 'In Progress')
                            <span class="badge bg-primary">{{ $request->Status }}</span> <!-- Blue -->
                            @elseif($request->Status === 'Under Review')
                            <span class="badge"
                                style="background-color: orange; color: black;">{{ $request->Status }}</span>
                            <!-- Orange -->
                            @elseif($request->Status === 'Needs Revision')
                            <span class="badge bg-danger">{{ $request->Status }}</span> <!-- Red -->
                            @elseif($request->Status === 'Completed')
                            <span class="badge bg-success">{{ $request->Status }}</span> <!-- Green -->
                            @else
                            <span class="badge bg-secondary">{{ $request->Status }}</span> <!-- Default (Gray) -->
                            @endif
                        </td>
                        <td>{{ $request->First_Name }}</td>
                        <td>{{ $request->Last_Name }}</td>
                        <td>{{ $request->Nationality }}</td>
                        <td>{{ $request->Location }}</td>
                        <td>{{ $request->Format }}</td>
                        <td>
                            <button class="btn btn-info btn-sm viewAttachmentsBtn"
                                data-attachments='@json(json_decode($request->Attachment, true))' data-bs-toggle="modal"
                                data-bs-target="#attachmentsModal" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="View Attachments"> <i class="bi bi-paperclip"></i>
                                <!-- View Attachments -->
                            </button>
                        </td>

                        <td data-order="{{ \Carbon\Carbon::parse($request->Updated_Time)->timestamp }}">
                            {{ \Carbon\Carbon::parse($request->Updated_Time)->format('M j, Y, h:i A') }}
                            <br>
                            <small class="text-muted">
                                ({{ \Carbon\Carbon::parse($request->Updated_Time)->diffForHumans() }})
                            </small>
                        </td>

                        <!-- ✅ Display Uploaded Format -->
                        <!-- Upload Button - Opens Upload Modal -->
                        <td id="uploadedFormat-{{ $request->Request_ID }}">
                            <button
                                class="btn btn-sm {{ in_array($request->Status, ['In Progress', 'Needs Revision']) ? 'btn-success' : 'btn-secondary' }} openUploadModalBtn"
                                data-id="{{ $request->Request_ID }}"
                                data-requested-by="{{ $request->user->first_name ?? '' }} {{ $request->user->last_name ?? '' }}"
                                data-applicant-name="{{ $request->First_Name }} {{ $request->Last_Name }}"
                                data-requested-format="{{ $request->Format }}"
                                data-files='@json(json_decode($request->uploaded_format, true) ?? [])'
                                data-feedback="{{ $request->feedback ?? 'No feedback provided' }}"
                                data-status="{{ $request->Status }}"
                                {{ in_array($request->Status, ['In Progress', 'Needs Revision']) ? '' : 'disabled' }}
                                data-bs-toggle="modal" data-bs-target="#uploadModal" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Upload and send the requested format">
                                <i class="bi bi-upload"></i>
                            </button>
                        </td>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Table 2: Pending Requests -->
            <table id="pendingRequestsTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Status</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Nationality</th>
                        <th>Location</th>
                        <th>Format</th>
                        <th>Attachment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="pendingRequestsTable">
                    @foreach ($profiles as $index => $profile)
                    <tr class="pending-row">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @php
                            $statusColors = [
                            'Pending' => 'warning', // Yellow
                            'In Progress' => 'primary', // Blue
                            'Under Review' => 'orange', // Orange (custom class)
                            'Needs Revision' => 'danger', // Red
                            'Completed' => 'success' // Green
                            ];
                            $colorClass = $statusColors[$profile->Status] ?? 'secondary';
                            @endphp

                            <span class="badge bg-{{ $colorClass }}">{{ $profile->Status }}</span>
                        </td>
                        <td>{{ $profile->First_Name }}</td>
                        <td>{{ $profile->Last_Name }}</td>
                        <td>{{ $profile->Nationality }}</td>
                        <td>{{ $profile->Location }}</td>
                        <td>{{ $profile->Format }}</td>
                        <td>
                            @php
                            $attachments = json_decode($profile->Attachment, true);
                            @endphp

                            @if (!empty($attachments) && is_array($attachments))
                            {{ count($attachments) }} file(s)
                            @else
                            No attachment
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm viewRequestBtn" data-id="{{ $profile->Request_ID }}"
                                data-first-name="{{ $profile->First_Name }}" data-last-name="{{ $profile->Last_Name }}"
                                data-nationality="{{ $profile->Nationality }}" data-location="{{ $profile->Location }}"
                                data-format="{{ $profile->Format }}"
                                data-requested-by="{{ $profile->user->first_name ?? '' }} {{ $profile->user->last_name ?? '' }}"
                                data-date-created="{{ \Carbon\Carbon::parse($profile->Date_Created)->format('M j, Y, h:i A') }}"
                                data-bs-toggle="modal" data-bs-target="#viewRequestModal">
                                View & Accept
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>



            <!-- Table 3: Completed Requests -->
            <table id="completedRequestsTable" class="table table-bordered table-striped" style="display: none;">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Status</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Nationality</th>
                        <th>Location</th>
                        <th>Format</th>
                        <th>Attachments</th>
                        <th>Date Accepted</th>
                        <th>Submit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($completedRequests as $index => $request)
                    <tr class="completed-row" data-status="{{ $request->Status }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($request->Status === 'Completed')
                            <span class="badge bg-success">{{ $request->Status }}</span>
                            @else
                            <span class="badge bg-secondary">{{ $request->Status }}</span>
                            @endif
                        </td>
                        <td>{{ $request->First_Name }}</td>
                        <td>{{ $request->Last_Name }}</td>
                        <td>{{ $request->Nationality }}</td>
                        <td>{{ $request->Location }}</td>
                        <td>{{ $request->Format }}</td>
                        <td>
                            <button class="btn btn-info btn-sm viewAttachmentsBtn"
                                data-attachments='@json(json_decode($request->Attachment, true))' data-bs-toggle="modal"
                                data-bs-target="#attachmentsModal" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="View Attachments">
                                <i class="bi bi-paperclip"></i>
                            </button>
                        </td>
                        <td>{{ $request->Updated_Time }}</td>
                        <td>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="bi bi-upload"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    </div>
    <!-- View & Accept Request Modal -->
    <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <!-- Increased width for better spacing -->
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRequestModalLabel">View Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <input type="hidden" id="request_id" name="id">

                    <!-- Request Info -->
                    <div class="mb-3">
                        <div class=" justify-content-between">
                            <div><strong>Requested By:</strong> <span id="view_requested_by"></span></div><br>
                            <div><strong>Date Created:</strong> <span id="view_date_created"></span></div>
                        </div>
                    </div>

                    <hr> <!-- Divider for clarity -->

                    <!-- User Details in Two Columns -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="view_first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="view_first_name" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="view_last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="view_last_name" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="view_nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="view_nationality" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="view_location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="view_location" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="view_format" class="form-label">Format</label>
                                <input type="text" class="form-control" id="view_format" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary acceptRequestBtn">Accept Request</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Attachment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Upload Form -->
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="upload_request_id" name="request_id">

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <!-- Request Details -->
                        <div class="mb-1">
                            <p><strong>Requested By:</strong> <span id="requestedBy"></span></p>
                            <p><strong>Name of Applicant:</strong> <span id="applicantName"></span></p>
                            <p><strong>Requested Format:</strong> <span id="requestedFormat"></span></p>
                        </div>

                        <!-- Separator Line -->
                        <hr>

                        <!-- Feedback Section (Hidden by Default) -->
                        <div id="feedbackSection" class=" d-none">
                            <b>Message/Feedback</b> 
                            <p id="uploadMessage" class="form-control-static rounded p-2 bg-white" 
   style="background-color: white; padding: 3px; border: 1px solid #a3cae9;">
</p>
                                                </div>

                        <!-- File Upload -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <b>Upload Files</b>
                                <label for="fileUpload" class="form-label ms-3 mt-2">Select File(s)</label>
                            </div>

                            <input type="file" class="form-control" id="fileUpload" name="uploaded_format[]" multiple>
                            <small class="form-text">You can select multiple files.</small>
                        </div>

                        <!-- Error Message -->
                        <div id="uploadFeedback" class="text-danger d-none">Please select at least one file.</div>

                        <!-- Uploaded Files Section -->
                        <div class="mt-2">
                            <b>Uploaded Files</b>
                            <div id="existingUploadedFiles" class="border rounded p-2 mt-2"></div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Confirmation Modal for Forwarding -->
    <div class="modal fade" id="forwardConfirmModal" tabindex="-1" aria-labelledby="forwardConfirmLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forwardConfirmLabel">Confirm Forwarding</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Files are successfuly uploaded. Now, are you sure you want to forward this request? This action
                    cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmForwardBtn">Yes, Forward</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Attachments Modal -->
    <div class="modal fade" id="attachmentsModal" tabindex="-1" aria-labelledby="attachmentsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachmentsModalLabel">Attachments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="attachmentsList" class="list-group">
                        <!-- Attachments will be inserted dynamically here -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/supportmanage.js') }}"></script>

</body>

<style>
.filter-btn.active {
    color: white !important;
}

/* Add specific colors when active */
.filter-btn.active[data-filter="all"] {
    background-color: #343a40 !important;
    /* Yellow */
    border-color: #343a40 !important;
}

/* Add specific colors when active */
.filter-btn.active[data-filter="Pending"] {
    background-color: #ffc107 !important;
    /* Yellow */
    border-color: #ffc107 !important;
}

.filter-btn.active[data-filter="In Progress"] {
    background-color: #0d6efd !important;
    /* Blue */
    border-color: #0d6efd !important;
}

.filter-btn.active[data-filter="Under Review"] {
    background-color: orange !important;
    /* Orange */
    border-color: orange !important;
}

.filter-btn.active[data-filter="Needs Revision"] {
    background-color: #dc3545 !important;
    /* Red */
    border-color: #dc3545 !important;
}

.filter-btn.active[data-filter="Completed"] {
    background-color: #198754 !important;
    /* Green */
    border-color: #198754 !important;
}


#acceptedRequestsTable,
#pendingRequestsTable,
#acceptedRequestsHeading,
#pendingRequestsHeading,
#acceptedRequestsTable_wrapper,
#pendingRequestsTable_wrapper {
    display: none;
    /* Hide initially */
}



.filter-btn {
    position: relative;
}


.new-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    /* ✅ Changed from left to right */
    background-color: red;
    color: white;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    border-radius: 8px;
    z-index: 10;
}


/* Base Styling */
html,
body {
    height: 100%;
    overflow: hidden;
    /* Hide built-in scrollbar */
    font-family: 'Poppins', sans-serif;
}

/* Create a scrollable container */
.scroll-container {
    height: 100vh;
    overflow-y: auto;
    padding: 10px;
    box-sizing: border-box;
    padding-bottom: 100px;
    /* Extra space at the bottom */

}

/* Custom Scrollbar - Webkit (Chrome, Edge, Safari) */
.scroll-container::-webkit-scrollbar {
    width: 8px;
}

.scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    /* Light background */
    border-radius: 10px;
}

.scroll-container::-webkit-scrollbar-thumb {
    background: rgba(31, 51, 95, 0.6);
    /* Blue scrollbar */
    border-radius: 10px;
    transition: background 0.3s ease;
}

.scroll-container::-webkit-scrollbar-thumb:hover {
    background: rgba(31, 51, 95, 0.8);
    /* Darker blue on hover */
}

/* Firefox scrollbar */
.scroll-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(31, 51, 95, 0.6) #f1f1f1;
}

/* Hover effect for Firefox */
.scroll-container:hover {
    scrollbar-color: rgba(31, 51, 95, 0.8) #f1f1f1;
}

#feedbackSection h6, 
#feedbackSection p {
    display: block; /* Ensures they stack */
    width: 100%; /* Optional: Makes them full-width */
}


/* Global Font & Styling */
body,
table,
.dataTables_filter input,
.dataTables_length select {
    font-family: 'Poppins', sans-serif;
}

/* Search Bar */
.dataTables_filter {
    text-align: center;
    margin-bottom: 15px;
}

.dataTables_filter input {
    width: 250px;
    padding: 10px;
    font-size: 14px;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.3s ease-in-out, box-shadow 0.3s;
    border-radius: 15px !important;

}

/* Styling for "Show X entries" */
.dataTables_length label {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    font-weight: 500;
}

/* Styling for the dropdown itself */
.dataTables_length select {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    padding: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    outline: none;
}

/* Styling for "Search:" label */
.dataTables_filter label {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    font-weight: 500;
}


#acceptedRequestsTable,
#pendingRequestsTable,
#completedRequestsTable {
    width: 100%;
    border-collapse: collapse;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    background: #1f335f;
    font-size: 12px;
}

#acceptedRequestsTable th,
#pendingRequestsTable th,
#completedRequestsTable th {
    background: #1f335f;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    /* Centers vertically as well */
}


#acceptedRequestsTable td,
#pendingRequestsTable td,
#completedRequestsTable td {
    padding: 14px 16px;
    text-align: left;
    background: #fff;
    text-align: center;
    vertical-align: middle;
    /* Centers vertically as well */
}


/* Row Styling */
#acceptedRequestsTable tbody tr,
#pendingRequestsTable tbody tr,
#completedRequestsTable tbody tr {
    transition: background 0.3s, transform 0.2s ease-in-out;
}

#pendingRequestsTable tr,
#completedRequestsTable tr {
    height: 70px;
    /* Adjust this for more spacing */
}

#pendingRequestsTable td,
#completedRequestsTable td {
    padding: 20px 16px;
    /* Increase top & bottom padding */
}





/* Pagination */
.dataTables_paginate {
    text-align: center;
    margin-top: 20px;
}

.dataTables_paginate .paginate_button {
    font-family: 'Poppins', sans-serif;

    border-color: #1f335f !important;
    color: white !important;
    padding: 8px 12px;
    margin: 0 5px;
    border-radius: 15px !important;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}

.dataTables_paginate .paginate_button:hover {
    background: #1f335f !important;
    transform: scale(1.1);
    color: white !important;
}

.dataTables_paginate .paginate_button.current {
    background: #A3CAE9 !important;
    font-weight: bold;
}

.dataTables_paginate .paginate_button.disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Entries Info Styling */
.dataTables_info {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    font-weight: 500;
    margin-top: 10px;
    text-align: left;
    border-radius: 6px;
    display: inline-block;
}







/* CSS FOR MODALS*/

/* Modal Background Overlay */
.modal-backdrop {
    background-color: rgba(31, 51, 95, 0.8);
}

/* Modal Content Styling */
.modal-content {
    background-color: #f4f8ff;
    /* Light blue background */
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border: 1px solid #fff;
}

/* Modal Header */
.modal-header {
    background-color: #1f335f;
    color: white;
    border-bottom: 2px solid #A3CAE9;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.modal-title {
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-weight: 600;
}

/* Close Button */
.btn-close {
    background-color: #A3CAE9;
    border-radius: 50%;
    opacity: 1;
}

.btn-close:hover {
    background-color: rgb(255, 255, 255);
    opacity: 0.8;
}

/* Modal Body */
.modal-body {
    padding: 20px;
}

/* Form Labels */
.form-label {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #1f335f;
    font-weight: 500;
}

/* Form Inputs & Select */
.form-control,
.form-select {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    border: 1px solid #A3CAE9;
    border-radius: 8px;
    padding: 10px;
    transition: border-color 0.3s ease-in-out;
}

.form-control:focus,
.form-select:focus {
    border-color: #1f335f;
    box-shadow: 0 0 10px rgba(31, 51, 95, 0.2);
}

/* Profile Image */
#profileImage {
    border: 2px solid #A3CAE9;
}

/* File Input Styling */
input[type="file"] {
    font-size: 14px;
    color: #1f335f;
}

/* Modal Footer */
.modal-footer {
    background-color: #f4f8ff;
    border-top: 1px solid #A3CAE9;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
}


/* Filter Button Container */
.mb-3 {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    /* Reduce space between buttons */
    justify-content: left;
    align-items: center;
}

/* Filter Buttons - Smaller & More Compact */
.filter-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    /* Reduce space inside the button */
    min-width: 120px;
    /* Decrease width */
    padding: 6px 10px;
    /* Reduce padding for a smaller look */
    border-radius: 6px;
    /* Slightly smaller rounded corners */
    font-size: 12px;
    /* Reduce font size */
    font-weight: 500;
    transition: all 0.3s ease-in-out;
    border: 2px solid transparent;
    text-align: center;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
}


/* Keep button colors consistent */
.btn-outline-dark {
    border-color: #343a40;
    color: #343a40;
}

.btn-outline-warning {
    border-color: #ffc107;
    color: #856404;
}

.btn-outline-primary {
    border-color: #007bff;
    color: #0056b3;
}

.btn-outline-orange {
    border-color: #fd7e14;
    color: #b45309;
}

.btn-outline-danger {
    border-color: #dc3545;
    color: #721c24;
}

.btn-outline-success {
    border-color: #28a745;
    color: #155724;
}


.btn-outline-dark {
    color: #343a40 !important;
    border-color: #343a40 !important;
}

.btn-outline-dark:hover {
    background-color: #343a40 !important;
    color: white !important;
}

.btn-outline-warning {
    color: #343a40 !important;
    border-color: #ffc107 !important;
}

.btn-outline-warning:hover {
    background-color: #ffc107 !important;
    color: white !important;
}

.btn-outline-primary {
    color: #343a40 !important;
    border-color: #007bff !important;
}

.btn-outline-primary:hover {
    background-color: #007bff !important;
    color: white !important;
}

.btn-outline-orange {
    color: #343a40 !important;
    border-color: orange !important;
}

.btn-outline-orange:hover {
    background-color: orange !important;
    color: white !important;
}

.btn-outline-danger {
    color: #343a40 !important;
    border-color: #dc3545 !important;
}

.btn-outline-danger:hover {
    background-color: #dc3545 !important;
    color: white !important;
}

.btn-outline-success {
    color: #343a40 !important;
    border-color: #28a745 !important;
}

.btn-outline-success:hover {
    background-color: #28a745 !important;
    color: white !important;
}

/* Hover Effect */
.filter-btn:hover {
    transform: translateY(-2px);
    background-color: rgba(255, 255, 255, 0.1);
    box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.15);
}

/* Active State */
.filter-btn.active {
    background-color: rgba(31, 51, 95, 0.15);
    border-width: 2px;
}

/* Status Badge - Smaller & Neater */
.status-badge {
    padding: 2px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

/* New Badge - Stylish Floating */
.new-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 6px;
    border-radius: 10px;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2);
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .filter-btn {
        min-width: 120px;
        font-size: 12px;
        padding: 8px 12px;
    }

    .status-badge {
        font-size: 10px;
        padding: 3px 8px;
    }

    .new-badge {
        font-size: 9px;
        padding: 2px 5px;
    }
}


/* Table Styling */
.table {
    width: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Flex container for toggle button and filters */
.filter-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Toggle Button */
#toggle-btn {
    min-width: 100px;
    padding: 8px 12px;
    margin-bottom: 30px;
}

/* Filter Buttons (Initially Hidden) */
.filter-buttons {
    display: none;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 30px;

}

/* Show filters when active */
.filter-buttons.show {
    display: flex;
}

/* Filter Button Styles */
.filter-btn {
    padding: 6px 12px;
    font-size: 13px;
    min-width: 120px;
    border-radius: 6px;
}
</style>

</html>