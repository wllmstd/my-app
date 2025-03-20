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

            <div class="mb-3">
                <button class="btn btn-outline-dark filter-btn active" data-filter="all">
                    <div class="status-label-wrapper">
                        All Tables
                        <span id="count-all" class="status-badge">0</span>
                    </div>
                </button>
                <button class="btn btn-outline-warning filter-btn" data-filter="Pending">
                    <span class="new-badge" id="new-badge-pending">NEW</span>
                    <div class="status-label-wrapper">
                        Pending Requests
                        <span id="count-pending" class="status-badge">0</span>
                    </div>
                </button>
                <button class="btn btn-outline-primary filter-btn" data-filter="In Progress">
                    <span class="new-badge" id="new-badge-in-progress">NEW</span>
                    <div class="status-label-wrapper">
                        In Progress
                        <span id="count-in-progress" class="status-badge">0</span>
                    </div>
                </button>
                <button class="btn btn-outline-orange filter-btn" data-filter="Under Review">
                    <span class="new-badge" id="new-badge-under-review">NEW</span>
                    <div class="status-label-wrapper">
                        Under Review
                        <span id="count-under-review" class="status-badge">0</span>
                    </div>
                </button>
                <button class="btn btn-outline-danger filter-btn" data-filter="Needs Revision">
                    <span class="new-badge" id="new-badge-needs-revision">NEW</span>
                    <div class="status-label-wrapper">
                        Needs Revision
                        <span id="count-needs-revision" class="status-badge">0</span>
                    </div>
                </button>
                <button class="btn btn-outline-success filter-btn" data-filter="Completed">
                    <span class="new-badge" id="new-badge-completed">NEW</span>
                    <div class="status-label-wrapper">
                        Completed
                        <span id="count-completed" class="status-badge">0</span>
                    </div>
                </button>
            </div>



            <!-- Table 1: My Accepted Requests -->
            <h2 id="acceptedRequestsHeading">My Accepted Requests</h2>
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
            <h2 id="pendingRequestsHeading">All Pending Requests</h2>
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

        </div>
    </div>


    <!-- View & Accept Request Modal -->
    <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRequestModalLabel">View Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="request_id" name="id">
                    <!-- Requested By -->
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label mb-0" style="white-space: nowrap;"><strong>Requested
                                By:</strong></label>
                        <span class="ms-2" id="view_requested_by"></span>
                    </div>

                    <!-- Date Created -->
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label mb-0" style="white-space: nowrap;"><strong>Date
                                Created:</strong></label>
                        <span class="ms-2" id="view_date_created"></span>
                    </div>
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

                    <div class="mb-3">
                        <label for="view_location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="view_location" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="view_format" class="form-label">Format</label>
                        <input type="text" class="form-control" id="view_format" readonly>
                    </div>
                </div>

                <div class="modal-footer">
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
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Attachment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="upload_request_id" name="request_id">

                    <div class="modal-body">
                        <p><strong>Requested By:</strong> <span id="requestedBy"></span></p>
                        <p><strong>Name of Applicant:</strong> <span id="applicantName"></span></p>
                        <p><strong>Requested Format:</strong> <span id="requestedFormat"></span></p>

                        <div id="feedbackSection" class="mt-3 d-none">
                            <strong><label for="uploadMessage" class="form-label">Message/Feedback</label></strong>
                            <p id="uploadMessage" class="form-control-static border rounded p-2 bg-light"></p>
                        </div>

                        <div class="mb-3">
                            <label for="fileUpload" class="form-label">Select File(s)</label>
                            <input type="file" class="form-control" id="fileUpload" name="uploaded_format[]" multiple>
                        </div>

                        <div id="uploadFeedback" class="text-danger d-none">Please select at least one file.</div>

                        <h6 class="mt-3">Uploaded Files:</h6>
                        <div id="existingUploadedFiles" class="mb-3"></div>
                    </div>

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
    .filter-btn {
        border-radius: 50px;
        /* Make buttons more circular */
        padding: 6px 14px;
    }

    .filter-btn {
        border-radius: 50px;
        /* Make buttons more circular */
        padding: 6px 14px;
    }

    .filter-btn.active {
        color: white !important;
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

    .btn-outline-orange {
        color: orange !important;
        border-color: orange !important;
    }

    .btn-outline-orange:hover {
        background-color: orange !important;
        color: white !important;
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
</style>

</html>