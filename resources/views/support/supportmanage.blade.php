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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- jQuery & DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


</head>

<body>

    <!-- Include the navbar here -->
    @include('support.support_navbar')

    <div class="container mt-4">

        <div class="mb-3">
            <button class="btn btn-outline-dark filter-btn active" data-filter="all">All Tables</button>
            <button class="btn btn-outline-warning filter-btn" data-filter="Pending">Pending Requests</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="In Progress">In Progress</button>
            <button class="btn btn-outline-orange filter-btn" data-filter="Under Review">Under Review</button>
            <button class="btn btn-outline-danger filter-btn" data-filter="Needs Revision">Needs Revision</button>
            <button class="btn btn-outline-success filter-btn" data-filter="Completed">Completed</button>
        </div>

        <!-- Table 1: My Accepted Requests -->
        <h2 id="acceptedRequestsHeading">My Accepted Requests</h2>
        <table id="acceptedRequestsTable" class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Nationality</th>
                    <th>Location</th>
                    <th>Attachments</th>
                    <th>Format</th>
                    <th>Date Accepted</th>
                    <th>Uploads</th>
                    <th>Actions</th>

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
                    <td>
                        <button class="btn btn-info btn-sm viewAttachmentsBtn"
                            data-attachments='@json(json_decode($request->Attachment, true))' data-bs-toggle="modal"
                            data-bs-target="#attachmentsModal">
                            View Attachments
                        </button>


                    </td>
                    <td>{{ $request->Format }}</td>
                    <td>{{ $request->Updated_Time }}</td>


                    <!-- ✅ Display Uploaded Format -->

                    <td id="uploadedFormat-{{ $request->Request_ID }}">
                        <!-- Upload Button - Opens Upload Modal -->
                        <button
                            class="btn btn-sm {{ in_array($request->Status, ['In Progress', 'Needs Revision']) ? 'btn-warning' : 'btn-secondary' }} openUploadModalBtn"
                            data-id="{{ $request->Request_ID }}"
                            data-files='@json(json_decode($request->uploaded_format, true) ?? [])'
                            {{ in_array($request->Status, ['In Progress', 'Needs Revision']) ? '' : 'disabled' }}
                            data-bs-toggle="modal" data-bs-target="#uploadModal">
                            Upload Files
                        </button>
                    </td>

                    <td>
                        <!-- Send for Review Button -->
                        <button
                            class="btn btn-sm {{ in_array($request->Status, ['In Progress', 'Needs Revision']) ? 'btn-success' : 'btn-secondary' }} forwardRequestBtn"
                            data-id="{{ $request->Request_ID }}"
                            {{ in_array($request->Status, ['In Progress', 'Needs Revision']) ? '' : 'disabled' }}>
                            Send for Review
                        </button>
                    </td>





                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Table 2: Pending Requests -->
        <h2 id="pendingRequestsHeading">All Pending Requests</h2>
        <table  id="pendingRequestsTable" class="table table-bordered">
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
                    <th>Date Created</th>
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
                    <td>{{ $profile->Date_Created }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm viewRequestBtn" data-id="{{ $profile->Request_ID }}"
                            data-first-name="{{ $profile->First_Name }}" data-last-name="{{ $profile->Last_Name }}"
                            data-nationality="{{ $profile->Nationality }}" data-location="{{ $profile->Location }}"
                            data-format="{{ $profile->Format }}" data-attachments="{{ $profile->Attachment }}"
                            data-bs-toggle="modal" data-bs-target="#viewRequestModal">
                            View & Accept
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

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
                    <input type="hidden" id="upload_request_id" name="request_id"> <!-- Store Request ID -->

                    <div class="modal-body">
                        <h6>Existing Files:</h6>
                        <div id="existingUploadedFiles" class="mb-3"></div> <!-- ✅ Files + Trash Buttons Here -->

                        <label for="fileUpload" class="form-label">Select File(s)</label>
                        <input type="file" class="form-control" id="fileUpload" name="uploaded_format[]" multiple>

                        <div id="uploadFeedback" class="text-danger d-none">Please select at least one file.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Forward Confirmation Modal -->
    <div class="modal fade" id="forwardConfirmModal" tabindex="-1" aria-labelledby="forwardConfirmLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forwardConfirmLabel">Confirm Forwarding</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to forward this request? This action cannot be undone.
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
    border-radius: 50px; /* Make buttons more circular */
    padding: 6px 14px;
}

.filter-btn {
    border-radius: 50px; /* Make buttons more circular */
    padding: 6px 14px;
}

.filter-btn.active {
    color: white !important;
}

/* Add specific colors when active */
.filter-btn.active[data-filter="Pending"] {
    background-color: #ffc107 !important; /* Yellow */
    border-color: #ffc107 !important;
}

.filter-btn.active[data-filter="In Progress"] {
    background-color: #0d6efd !important; /* Blue */
    border-color: #0d6efd !important;
}

.filter-btn.active[data-filter="Under Review"] {
    background-color: orange !important; /* Orange */
    border-color: orange !important;
}

.filter-btn.active[data-filter="Needs Revision"] {
    background-color: #dc3545 !important; /* Red */
    border-color: #dc3545 !important;
}

.filter-btn.active[data-filter="Completed"] {
    background-color: #198754 !important; /* Green */
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

#acceptedRequestsTable, #pendingRequestsTable, #acceptedRequestsHeading, #pendingRequestsHeading {
    display: none; /* Hide tables initially */
}

</style>

</html>