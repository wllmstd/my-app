<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Bundle (JS & Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" defer></script>

</head>

<body>

    <!-- Include the navbar here -->
    @include('user.user_navbar')

    <div class="container mt-4">


        <h2>User Requests</h2>
        <div class="mb-3">
            <button class="btn btn-outline-dark filter-btn active" data-filter="all">All Tables</button>
            <button class="btn btn-outline-warning filter-btn" data-filter="Pending">Pending Requests</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="In Progress">In Progress</button>
            <button class="btn btn-outline-orange filter-btn" data-filter="Under Review">Under Review</button>
            <button class="btn btn-outline-danger filter-btn" data-filter="Needs Revision">Needs Revision</button>
            <button class="btn btn-outline-success filter-btn" data-filter="Completed">Completed</button>
        </div>

        <!-- Add Request Button -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addRequestModal">
                <i class="bi bi-plus-circle"></i> Add Request
            </button>
        </div>

        <!-- Table to display requests -->
        <table class="table table-bordered table-striped" id="requestTable">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Nationality</th>
                    <th>Location</th>
                    <th>Format</th>
                    <th>Updated Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $index => $request)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($request->Status === 'Pending')
                        <span class="badge bg-warning text-dark">{{ $request->Status }}</span>
                        @elseif($request->Status === 'In Progress')
                        <span class="badge bg-primary">{{ $request->Status }}</span>
                        @elseif($request->Status === 'Under Review')
                        <span class="badge"
                            style="background-color: orange; color: black;">{{ $request->Status }}</span>
                        @elseif($request->Status === 'Needs Revision')
                        <span class="badge bg-danger">{{ $request->Status }}</span>
                        @elseif($request->Status === 'Completed')
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
                    <td data-order="{{ \Carbon\Carbon::parse($request->Updated_Time)->timestamp }}">
                        {{ \Carbon\Carbon::parse($request->Updated_Time)->format('M j, Y, h:i A') }}
                        <br>
                        <small class="text-muted">
                            ({{ \Carbon\Carbon::parse($request->Updated_Time)->diffForHumans() }})
                        </small>
                    </td>

                    <td>
                        <button class="btn btn-primary btn-sm viewRequestBtn" data-id="{{ $request->Request_ID }}"
                            data-first-name="{{ $request->First_Name }}" data-last-name="{{ $request->Last_Name }}"
                            data-nationality="{{ $request->Nationality }}" data-location="{{ $request->Location }}"
                            data-format="{{ $request->Format }}" data-attachments="{{ $request->Attachment }}"     
                            data-date-created="{{ \Carbon\Carbon::parse($request->Date_Created)->format('M j, Y, h:i A') }}"
                            data-bs-toggle="modal" data-bs-target="#viewRequestModal" title="View request details">
                            <i class="bi bi-eye"></i>
                        </button>


                        <button class="btn btn-warning btn-sm reviewSubmissionBtn" data-id="{{ $request->Request_ID }}"
                            data-first-name="{{ $request->First_Name }}" data-last-name="{{ $request->Last_Name }}"
                            data-nationality="{{ $request->Nationality }}" data-location="{{ $request->Location }}"
                            data-format="{{ $request->Format }}" data-uploaded-format="{{ $request->uploaded_format }}"
                            data-profiler="{{ $request->Profiler_Name }}"
                            title="Check if the submitted file meets the requested format">
                            <i class="bi bi-file-earmark-check"></i>
                        </button>

                        <button class="btn btn-danger btn-sm deleteRequestBtn" data-id="{{ $request->Request_ID }}"
                            data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete this request">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <!-- Add Request Modal -->
    <div class="modal fade" id="addRequestModal" tabindex="-1" aria-labelledby="addRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRequestModalLabel">Add Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" class="form-control" id="nationality" name="nationality" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="format" class="form-label">Format</label>
                            <select class="form-select" id="format" name="format" required>
                                <option value="Geco Standard">Geco Standard</option>
                                <option value="Geco New Date">Geco New Date</option>
                                <option value="Geco New Rate">Geco New Rate</option>
                                <option value="Blind">Blind</option>
                                <option value="HTD">HTD</option>
                                <option value="SAP">SAP</option>
                                <option value="PCX">PCX</option>
                                <option value="Accenture">Accenture</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachments (PDF, DOCX)</label>
                            <input type="file" class="form-control" id="attachment" name="attachments[]"
                                accept=".pdf,.doc,.docx">
                        </div>

                        <!-- File Preview Section -->
                        <div id="fileList" class="mt-2"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Request Modal -->
    <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRequestModalLabel">View & Edit Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateRequestForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Laravel requires this for update requests -->
                    <input type="hidden" id="request_id" name="id"> <!-- Hidden field for ID -->

                    <div class="modal-body">
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label mb-0" style="white-space: nowrap;"><strong>Date
                                    Created:</strong></label>
                                    <span class="ms-2 date-created">N/A</span>

                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label mb-0" style="white-space: nowrap;"><strong>Created
                                    By:</strong></label>
                            <span class="ms-2">
                                {{ ($request->user)->first_name }}
                                {{ ($request->user)->last_name }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nationality" class="form-label">Nationality</label>
                            <input type="text" class="form-control" id="edit_nationality" name="nationality" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="edit_location" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_format" class="form-label">Format</label>
                            <select class="form-select" id="edit_format" name="format" required>
                                <option value="Geco Standard">Geco Standard</option>
                                <option value="Geco New Date">Geco New Date</option>
                                <option value="Geco New Rate">Geco New Rate</option>
                                <option value="Blind">Blind</option>
                                <option value="HTD">HTD</option>
                                <option value="SAP">SAP</option>
                                <option value="PCX">PCX</option>
                                <option value="Accenture">Accenture</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_attachments" class="form-label">Attachments</label>
                            <input type="file" class="form-control" id="edit_attachments" name="attachments[]" multiple>
                        </div>

                        <!-- Existing Attachments -->
                        <div id="existingAttachments">
                            <h6>Existing Attachments:</h6>
                            @php
                            $attachments = json_decode($request->Attachment, true);
                            @endphp
                            @if (!empty($attachments))
                            @foreach ($attachments as $file)
                            <div class="d-flex align-items-center border p-2 mb-1 rounded">
                                <a href="{{ asset('storage/attachments/' . $file) }}" target="_blank"
                                    class="me-auto">{{ $file }}</a>
                                <<button type="button" class="btn btn-sm btn-danger delete-attachment-btn"
                                    data-request-id="{{ $request->Request_ID }}" data-file-name="{{ $file }}">
                                    <i class="bi bi-trash"></i>
                                    </button>
                            </div>
                            @endforeach
                            @else
                            <p>No attachments found.</p>
                            @endif
                        </div>

                        <!-- Hidden Input for Deleted Files -->
                        <input type="hidden" name="deleted_files[]" id="deletedFilesInput">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <!-- Added modal-dialog-centered class here -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this request? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Attachment Confirmation Modal -->
    <div class="modal fade" id="deleteAttachmentModal" tabindex="-1" aria-labelledby="deleteAttachmentModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAttachmentModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this attachment?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body text-center">
                    <p id="successMessage">Request updated successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Modal -->
    <div class="modal fade" id="failedModal" tabindex="-1" aria-labelledby="failedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="failedModalLabel">Update Failed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p id="failedMessage">Failed to update the request. Please try again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Submission Modal -->
    <div class="modal fade" id="reviewSubmissionModal" tabindex="-1" aria-labelledby="reviewSubmissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewSubmissionModalLabel">Review Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Profiled by:</strong> <span id="reviewProfiler"></span></p>
                    <p><strong>Name of Applicant:</strong> <span id="reviewRequester"></span></p>
                    <p><strong>Requested Format:</strong> <span id="reviewFormat"></span></p>

                    <!-- <p><strong>Submitted File:</strong></p> -->
                    <div id="reviewUploadedFormat" class="mt-2"></div>

                    <label for="reviewFeedback">Feedback:</label>
                    <textarea id="reviewFeedback" class="form-control" rows="3"
                        placeholder="Provide feedback..."></textarea>

                    <div class="mt-3">
                        <button id="markAsDoneBtn" class="btn btn-success">Mark as Done</button>
                        <button id="reviseBtn" class="btn btn-danger">Request Revision</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

<style>
.viewRequestBtn:hover {
    background-color: #0b5ed7;
    /* Darker blue */
    transition: background-color 0.2s ease-in-out;
}

.reviewSubmissionBtn:hover {
    background-color: #d39e00;
    /* Darker yellow */
    transition: background-color 0.2s ease-in-out;
}

.deleteRequestBtn:hover {
    background-color: #bb2d3b;
    /* Darker red */
    transition: background-color 0.2s ease-in-out;
}

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
</style>

</html>

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<script src="{{ asset('js/usermanage.js') }}"></script>